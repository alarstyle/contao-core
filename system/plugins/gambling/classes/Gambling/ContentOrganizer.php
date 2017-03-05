<?php

namespace Gambling;

use Grow\Organizer;

class ContentOrganizer extends Organizer
{


    public function __construct()
    {
        parent::__construct('tl_content');
    }


    public function save($id, $fieldsValues)
    {
        $this->errorsArr = $this->validate($fieldsValues, $id);

        if (!empty($this->errorsArr)) {
            return null;
        }

        $this->doSaveCallbacks($fieldsValues, $id);

        $pageFields = [];
        $elements = [];

        foreach ($fieldsValues as $fieldName => $fieldValue) {
            if (strpos($fieldName, '::') <= 0) {
                $pageFields[$fieldName] = $fieldValue;
            }
            else {
                list($elementId, $elementFieldName) = explode('::', $fieldName);
                $elements[$elementId][$elementFieldName] = $fieldValue;
            }
        }

        $connection = \Grow\Database::getConnection();
        $currentCountryId = \Contao\Session::getInstance()->get('CurrentCountry');

        // Save Page Fields
        if (count($pageFields)) {
            $pageRow = $connection->selectQuery()->table('tl_page')
                ->where('id', $id)
                ->execute()->asArray();
            $updateQuery = $connection->updateQuery()->table('tl_page');
            $pageRow = $pageRow[0];
            foreach ($pageFields as $fieldName=>$fieldValue) {
                $values = $pageRow->$fieldName ? deserialize($pageRow->$fieldName) : [];
                $values[$currentCountryId] = $fieldValue;
                $updateQuery->set($fieldName, serialize($values));
            }
            $updateQuery->where('id', $id)->execute();
        }

        // Save Element Fields
        foreach ($elements as $elementId => $elementFields) {
            $elementRow = $connection->selectQuery()->table('tl_content')
                ->where('id', $elementId)
                ->where('ptable', 'tl_page')
                ->execute()->asArray();
            if (!count($elementRow)) continue;

            $patternValues = deserialize($elementRow[0]->pattern_data);
            $patternValues[$currentCountryId] = $elementFields;

            $updateQuery = $connection->updateQuery()->table('tl_content')
                ->set('pattern_data', serialize($patternValues))
                ->where('id', $elementId)
                ->where('ptable', 'tl_page')
                ->execute();
        }

        return null;
    }


    /**
     * @param $id String Page ID
     * @return array
     * @throws \Exception
     */
    public function load($id)
    {
        $connection = \Grow\Database::getConnection();

        $page = $connection->selectQuery()->table('tl_page')
            ->where('id', $id)
            ->execute()->asArray();

        if (!count($page)) return [];
        $page = $page[0];
        $currentCountryId = \Contao\Session::getInstance()->get('CurrentCountry');

        $content = $connection->selectQuery()->table('tl_content')
            ->where('pid', $id)
            ->where('ptable', 'tl_page')
            ->orderBy('sorting', 'asc')
            ->execute()->asArray();

        $pageFieldsNames = ['navigationTitle','metaTitle','metaDescription'];
        $pageFieldsValues = [];

        foreach ($pageFieldsNames as $fieldName) {
            $pageFieldsValues[$fieldName] = deserialize($page->$fieldName)[$currentCountryId];
        }

        $fields = [];

        $generalFields = $this->getUnitsDataForFields($pageFieldsValues, $pageFieldsNames, $GLOBALS['TL_DCA']['tl_page']['fields']);

        if ($generalFields) {
            $fields = array_merge(
                $fields,
                [
                    'general_title' => [
                        'component' => 'section-title',
                        'label' => 'General',
                    ]
                ],
                $generalFields
            );
        }

        foreach ($content as $element) {
            if (strpos($element->type, 'ptr_') !== 0) continue;
            $patternData = $this->loadPattern($element->id, $element->type);
            if (!$patternData || !$patternData['fields']) continue;

            $fieldsNames = [];
            foreach ($patternData['fields'] as $fieldName=>$fieldData) {
                $fieldsNames[] = $fieldName;
            }

            $patternValues = deserialize($element->pattern_data)[$currentCountryId];

            $tempUnitsData = $this->getUnitsDataForFields($patternValues, $fieldsNames, $patternData['fields']);

            if (!$tempUnitsData) continue;

            $unitsData = [];

            foreach ($tempUnitsData as $unitId=>$unitData) {
                $unitsData[$element->id . '::' . $unitId] = $unitData;
            }

            $sectionTitle = [];
            $sectionTitle['title_' . $element->id] = [
                'component' => 'section-title',
                'label' => $patternData['label'],
            ];

            $fields = array_merge(
                $fields,
                $sectionTitle,
                $unitsData
            );

        }

        return $fields;
    }


    public function getUnitsData($row = null)
    {
        $tableData = $GLOBALS['TL_DCA'][$this->table];

        $palette = $tableData['palettes'][$row['type'].'New'] ?: $tableData['palettes'][$row['type']];

        if (!$palette) return null;

        $fieldsNames = $this->getFieldsNamesFromPalette($palette);
        $fields = $this->getUnitsDataForFields($row, $fieldsNames);

        return $fields;
    }


    protected function loadPattern($elementId, $patternName)
    {
        $patternFile = TL_ROOT . '/templates/' . $patternName . '.php';
        if (!file_exists($patternFile)) return null;
        $patternData = include $patternFile;
        return $this->parseDCA($elementId, $patternData);
    }

    protected function parseDCA($elementId, $patternData)
    {
        $arrData = $patternData;

        $palette = '';

        if (empty($arrData))
        {
            return null;
        }

        if (empty($arrData['fields']))
        {
            return $arrData;
        }

        $arrFields = [];
        foreach($arrData['fields'] as $fieldName=>$fieldData){
            $arrFields[$fieldName] = $fieldData;
        }
        $arrData['fields'] = $arrFields;

        foreach ($arrData['fields'] as &$arrVar)
        {
            $arrEval = array();

            $arrVar['inputTypeOriginal'] = $arrVar['inputType'];

            switch($arrVar['inputType'])
            {
                case 'wysiwyg':
                    $arrVar['inputType'] = 'textarea';
                    $arrEval = array('rte'=>'tinyMCE', 'doNotSaveEmpty'=>true);
                    break;

                case 'html':
                    $arrVar['inputType'] = 'textarea';
                    $arrEval = array('allowHtml'=>true, 'class'=>'monospace', 'rte'=>'ace|html');
                    break;

                case 'checkbox':
                    $arrVar['inputType'] = 'checkbox';
                    break;

                case 'image':
                    $arrVar['inputType'] = 'fileTree';
                    $arrEval = array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>\Contao\Config::get('validImageTypes'));
                    break;

                case 'file':
                    $arrVar['inputType'] = 'fileTree';
                    $arrEval = array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true);
                    break;

                case 'folder':
                    $arrVar['inputType'] = 'fileTree';
                    $arrEval = array('fieldType'=>'radio');
                    break;

                case 'page':
                    $arrVar['inputType'] = 'pageTree';
                    $arrVar['foreignKey'] = 'tl_page.title';
                    $arrEval = array('fieldType'=>'radio');
                    $arrVar['relation'] = array('type'=>'hasOne', 'load'=>'lazy');
                    break;

                case 'date':
                    $arrVar['inputType'] = 'text';
                    $arrEval = array('rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'wizard');
                    break;

                case 'time':
                    $arrVar['inputType'] = 'text';
                    $arrEval = array('rgxp'=>'time', 'datepicker'=>true, 'tl_class'=>'wizard');
                    break;

                case 'datetime':
                    $arrVar['inputType'] = 'text';
                    $arrEval = array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'wizard');
                    break;

                case 'color':
                    $arrVar['inputType'] = 'text';
                    $arrEval = array('maxlength'=>6, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'wizard');
                    break;
            }

            if ($arrEval['tl_class'] && $arrVar['eval']['tl_class']) {
                $arrVar['eval']['tl_class'] .= ' ' . $arrEval['tl_class'];
            }

            $arrVar['eval'] = array_merge($arrEval, $arrVar['eval'] ?: array());

            if (is_string($arrVar['label'])) {
                $arrVar['label'] = [$arrVar['label'], ''];
            }
//            $arrVar['label'] = DynamicDCA::getLabelTranslation($arrVar['label'], $GLOBALS['TL_LANGUAGE']);
        }

        return $arrData;
    }

}
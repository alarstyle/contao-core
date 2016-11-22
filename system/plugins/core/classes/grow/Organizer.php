<?php

namespace Grow;

use Contao\Controller;
use Contao\Database;
use Contao\System;
use Grow\Units\AbstractUnit;

class Organizer
{

    protected $table;


    protected $database;


    public function __construct($tableName)
    {
        $this->table = $tableName;
        $this->database = Database::getInstance();

        System::loadLanguageFile($tableName);
        Controller::loadDataContainer($tableName);
    }


    public function blank()
    {
        return $this->getUnitsData();
    }


    public function load($id)
    {
        $objRow = $this->database->prepare("SELECT * FROM " . $this->table . " WHERE id=?")
            ->limit(1)
            ->execute($id);

        // Redirect if there is no record with the given ID
        if ($objRow->numRows < 1) {
            $this->log('Could not load record "' . $this->strTable . '.id=' . $this->intId . '"', __METHOD__, TL_ERROR);
            throw new \Exception('No record found');
        }

        $fetchedRow = $objRow->fetchAssoc();

        $unitsData = $this->getUnitsData($fetchedRow);

        return $unitsData;
    }


    public function create($fieldsValues)
    {

        foreach ($fieldsValues as $value) {

        }

        $errors = $this->validate($fieldsValues);

        if (!empty($errors)) {
            return $errors;
        }

        $objInsertStmt = $this->database->prepare("INSERT INTO " . $this->table . " %s")
            ->set($fieldsValues)
            ->execute();

        return true;
    }


    public function save($id, $fieldsValues)
    {
        $errors = $this->validate($fieldsValues);

        if (!empty($errors)) {
            return $errors;
        }

        $fieldsStr = '';
        $valuesArr = [];

        foreach ($fieldsValues as $field=>$value) {
            if (strlen($fieldsStr) > 0) $fieldsStr .= ', ';
            $fieldsStr .= $field . '=?';
            $valuesArr[] = $value;
        }

        $valuesArr[] = $id;

        $objUpdateStmt = $this->database->prepare("UPDATE " . $this->table . " SET " . $fieldsStr . " WHERE id=?")
            ->execute($valuesArr);

        return true;
    }


    public function delete($id)
    {
        if (empty($id)) {
            return 'ID is not set';
        }

        $this->database->prepare("DELETE FROM " . $this->table . " WHERE id=?")
            ->execute($id);

        return true;
    }


    public function disable($id)
    {
        if (empty($id)) {
            return 'ID is not set';
        }

        return true;
    }


    public function move()
    {

    }


    public function getListHeaders()
    {
        $tableData = $GLOBALS['TL_DCA'][$this->table];
        $listFields = $tableData['list']['label']['fields_new'] ?: $tableData['list']['label']['fields'];
        $headers = [];

        foreach ($listFields as $fieldName) {
            if (!isset($tableData['fields'][$fieldName])) continue;
            $field = $tableData['fields'][$fieldName];
            $headers[] = [
                'name' => $fieldName,
                'label' => $field['label'][0] ?: ''
            ];
        }

        $headers[] = [
            'name' => 'operations',
            'label' => ''
        ];

        return $headers;
    }


    public function getList($limit = 20, $skip = 0, $where = '')
    {
        $query = "SELECT * FROM " . $this->table . ' ' . $where;

        $objRowStmt = $this->database->prepare($query);

        $objRowStmt->limit($limit, $skip);

        $objRow = $objRowStmt->execute();

        if ($objRow->numRows < 1) {
            return 'No data';
        }

        $result = $objRow->fetchAllAssoc();

        $list = [];
        $tableData = $GLOBALS['TL_DCA'][$this->table];
        $listFields = $tableData['list']['label']['fields_new'] ?: $tableData['list']['label']['fields'];

        $operationsData = $tableData['list']['operations'];
        $operations = [];

        foreach ($operationsData as $operationName=>$operationData) {
            if (!isset($operationData['icon_new'])) continue;
            $operations[] = [
                'name' => $operationName,
                'label' => $operationData['label'][0] ?: $operationName,
                'icon' => $operationData['icon_new']
            ];
        }

        foreach ($result as $i => $item) {
            $itemData = [
                'id' => $item['id'],
                'fields' => []
            ];
            foreach ($listFields as $fieldName) {
                if (!isset($tableData['fields'][$fieldName])) continue;
                $itemData['fields'][] = $item[$fieldName] ?: '';
            }
            $itemData['fields'][]['operations'] = $operations;
            $list[] = $itemData;
        }

        return $list;
    }


    public function getUnitsData($row = NULL)
    {
        $tableData = $GLOBALS['TL_DCA'][$this->table];
        $palette = $tableData['palettes']['default'];
        $boxes = trimsplit(';', $palette);
        $fields = [];
        $fieldsNames = [];

        if (empty($boxes)) return $fields;

        foreach ($boxes as $k => $v) {
            $eCount = 1;
            $boxes[$k] = trimsplit(',', $v);

            foreach ($boxes[$k] as $kk => $vv) {
                if (preg_match('/^\[.*\]$/', $vv)) {
                    ++$eCount;
                    continue;
                }

                if (preg_match('/^\{.*\}$/', $vv)) {
                    continue;
                } elseif ($tableData['fields'][$vv]['exclude1'] || !is_array($tableData['fields'][$vv])) {
                    continue;
                }

                $fieldsNames[] = $vv;
            }
        }

        foreach ($fieldsNames as $fieldName) {
            $fieldData = $tableData['fields'][$fieldName];

            if (empty($fieldData['inputType'])) continue;

            $unitClass = $this->getUnitClass($fieldData['inputType']);

            if (empty($unitClass)) continue;

            /** @var AbstractUnit $unit */
            $unit = new $unitClass($this->table, $fieldName);

            $fields[] = $unit->getUnitData(!empty($row) ? $row[$fieldName] : NULL);
        }

        return $fields;
    }


    public function validate(&$fieldsValues)
    {
        $errors = [];
        $fieldsData = $GLOBALS['TL_DCA'][$this->table]['fields'];

        foreach ($fieldsValues as $field=>$value) {
            $unitClass = $this->getUnitClass($fieldsData[$field]['inputType']);
            /** @var AbstractUnit $unit */
            $unit = new $unitClass($this->table, $field);
            $processedValue = $unit->validate($value);
            if ($unit->hasErrors()) {
                $errors[$field] = $unit->getErrors();
            } else {
                $fieldsValues[$field] = $processedValue;
            }
        }

        return $errors;
    }


    protected function getUnitClass($unitName)
    {
        $class = $GLOBALS['UNITS'][$unitName];
        return class_exists($class) ? $class : NULL;
    }


    protected function getUnitComponent($unitName)
    {
        $class = $this->getUnitClass($unitName);
        return !empty($class) ? $class::$componentName : NULL;
    }

}
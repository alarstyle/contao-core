<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.11.16
 * Time: 21:27
 */

namespace Grow\Controllers;

use Contao\BackendTemplate;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Grow\ActionData;
use Grow\ApplicationData;
use Grow\Organizer;

class Users extends \Contao\Controllers\BackendMain
{

    public $ajaxActions = [
        'getGroups' => 'ajaxGetGroups',
        'getList' => 'ajaxGetList',
        'getListItem' => 'ajaxGetListItem',
        'saveItem' => 'ajaxSaveItem'
    ];

    protected $config = [];

    protected $groupTable = null;

    protected $listTable = null;

    protected $jsFile = '/system/plugins/core/assets/js/controllers/list.js';

    protected $listOrganizer;


    public function __construct($config = null)
    {
        $this->config = $config;

        if ($this->config['group']) {
            $this->groupTable = $this->config['group']['table'];
        }

        if ($this->config['list']) {
            $this->listTable = $this->config['list']['table'];
        }


        $this->listOrganizer = new Organizer($this->listTable);

        parent::__construct();
    }


    protected function generateMainSection()
    {
        $objTemplate = new BackendTemplate('be_users');

        $objTemplate->group = $this->generateGroup();
        $objTemplate->list = $this->generateList();
        $objTemplate->edit = $this->generateEdit();

        $this->Template->main = $objTemplate->parse();
    }


    protected function generateGroup()
    {
        return '';
    }


    protected function generateList()
    {

        //ApplicationData::addData('groups', []);
        //ApplicationData::addData('list', $list);
        //ApplicationData::addData('listHeaders', $listFields);

        //ApplicationData::addData('fields', $this->getFields());

        //ApplicationData::add('list', $result);

        return '';
    }


    protected function generateItemForList($item, $listFields)
    {
        $itemData = [
            'id' => $item['id']
        ];
        foreach ($listFields as $fieldName) {
            $itemData[$fieldName] = $item[$fieldName] ?: '';
        }

        return $itemData;
    }


    protected function getFields()
    {
        $strPalette = $GLOBALS['TL_DCA'][$this->listTable]['palettes']['default'];
        $boxes = trimsplit(';', $strPalette);
        $legends = [];
        $fields = [];

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
                } elseif ($GLOBALS['TL_DCA'][$this->listTable]['fields'][$vv]['exclude1'] || !is_array($GLOBALS['TL_DCA'][$this->listTable]['fields'][$vv])) {
                    continue;
                }
                $fieldsNames[] = $vv;
            }
        }

        foreach ($fieldsNames as $fieldName) {
            $fieldData = $GLOBALS['TL_DCA'][$this->listTable]['fields'][$fieldName];
            if (empty($fieldData['inputType'])) continue;
            $fields[] = [
                'name' => $fieldName,
                'type' => $fieldData['inputType'],
                'label' => is_array($fieldData['label']) ? $fieldData['label'][0] : $fieldName,
                'hint' => is_array($fieldData['label']) ? $fieldData['label'][1] : '',
                'value' => ''
            ];
        }

        return $fields;
    }

    protected function generateEdit()
    {
        return '';

        $strPalette = $GLOBALS['TL_DCA'][$this->listTable]['palettes']['default'];
        $boxes = trimsplit(';', $strPalette);
        $legends = array();
        $return = '';


        if (!empty($boxes)) {
            foreach ($boxes as $k => $v) {
                $eCount = 1;
                $boxes[$k] = trimsplit(',', $v);


                foreach ($boxes[$k] as $kk => $vv) {
                    if (preg_match('/^\[.*\]$/', $vv)) {
                        ++$eCount;
                        continue;
                    }

                    var_dump($vv);

                    if (preg_match('/^\{.*\}$/', $vv)) {
                        $legends[$k] = substr($vv, 1, -1);
                        unset($boxes[$k][$kk]);
                    } elseif ($GLOBALS['TL_DCA'][$this->listTable]['fields'][$vv]['exclude1'] || !is_array($GLOBALS['TL_DCA'][$this->listTable]['fields'][$vv])) {
                        unset($boxes[$k][$kk]);
                    }
                }

                // Unset a box if it does not contain any fields
                if (count($boxes[$k]) < $eCount) {
                    unset($boxes[$k]);
                }
            }

            $class = 'tl_tbox';
            $fs = $this->Session->get('fieldset_states');

            // Render boxes
            foreach ($boxes as $k => $v) {
                $strAjax = '';
                $blnAjax = false;
                $key = '';
                $cls = '';
                $legend = '';

                if (isset($legends[$k])) {
                    list($key, $cls) = explode(':', $legends[$k]);
                    $legend = "\n" . '<legend onclick="AjaxRequest.toggleFieldset(this,\'' . $key . '\',\'' . $this->listTable . '\')">' . (isset($GLOBALS['TL_LANG'][$this->listTable][$key]) ? $GLOBALS['TL_LANG'][$this->listTable][$key] : $key) . '</legend>';
                }

                if (isset($fs[$this->listTable][$key])) {
                    $class .= ($fs[$this->listTable][$key] ? '' : ' collapsed');
                } else {
                    $class .= (($cls && $legend) ? ' ' . $cls : '');
                }

                $return .= "\n\n" . '<fieldset' . ($key ? ' id="pal_' . $key . '"' : '') . ' class="' . $class . ($legend ? '' : ' nolegend') . '">' . $legend;

                // Build rows of the current box
                foreach ($v as $vv) {
                    if ($vv == '[EOF]') {
                        if ($blnAjax && Environment::get('isAjaxRequest')) {
                            return $strAjax . '<input type="hidden" name="FORM_FIELDS[]" value="' . specialchars($strPalette) . '">';
                        }

                        $blnAjax = false;
                        $return .= "\n" . '</div>';

                        continue;
                    }

                    if (preg_match('/^\[.*\]$/', $vv)) {
                        $thisId = 'sub_' . substr($vv, 1, -1);
                        $blnAjax = false;
                        $return .= "\n" . '<div id="' . $thisId . '">';

                        continue;
                    }

                    $this->strField = $vv;
                    $this->strInputName = $vv;
                    //$this->varValue = $objRow->$vv;

                    // Convert CSV fields (see #2890)
                    if ($GLOBALS['TL_DCA'][$this->listTable]['fields'][$this->strField]['eval']['multiple'] && isset($GLOBALS['TL_DCA'][$this->listTable]['fields'][$this->strField]['eval']['csv'])) {
                        $this->varValue = trimsplit($GLOBALS['TL_DCA'][$this->listTable]['fields'][$this->strField]['eval']['csv'], $this->varValue);
                    }

                    // Call load_callback
                    if (is_array($GLOBALS['TL_DCA'][$this->listTable]['fields'][$this->strField]['load_callback'])) {
                        foreach ($GLOBALS['TL_DCA'][$this->listTable]['fields'][$this->strField]['load_callback'] as $callback) {
                            if (is_array($callback)) {
                                $this->import($callback[0]);
                                $this->varValue = $this->{$callback[0]}->{$callback[1]}($this->varValue, $this);
                            } elseif (is_callable($callback)) {
                                $this->varValue = $callback($this->varValue, $this);
                            }
                        }
                    }

                    $this->objActiveRecord->{$this->strField} = $this->varValue;
                }

                $class = 'tl_box';
                $return .= "\n" . '</fieldset>';
            }
        }

        return $return;
    }


    public function ajaxGetList()
    {
        $query = "SELECT * FROM " . $this->listTable;

        $objRowStmt = $this->Database->prepare($query);

        $objRowStmt->limit(20, 0);

        $objRow = $objRowStmt->execute($this->values);

        if ($objRow->numRows < 1) {
            return 'No data';
        }

        $result = $objRow->fetchAllAssoc();

        $list = [];
        $listFields = $GLOBALS['TL_DCA'][$this->listTable]['list']['label']['fields_new'];

        foreach ($result as $i => $item) {
            $list[$i] = $this->generateItemForList($item, $listFields);
        }

        ActionData::data('list', $list);
    }


    public function ajaxGetGroups()
    {

    }


    public function ajaxGetListItem()
    {
        $id = Input::post('id');

        if ($id === 'new') {
            $fields = $this->listOrganizer->blank();
        }
        else {
            $fields = $this->listOrganizer->load($id);
        }

        ActionData::data('fields', $fields);
    }


    public function ajaxSaveItem()
    {
        $id = Input::post('id');
        $fields = Input::post('fields');

        if ($id === 'new') {
            $result = $this->listOrganizer->create($fields);
        }
        else {
            $result = $this->listOrganizer->save($id, $fields);
        }

        if ($result !== true) {
            ActionData::error($result);
        }
    }

}
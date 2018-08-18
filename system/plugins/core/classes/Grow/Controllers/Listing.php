<?php

namespace Grow\Controllers;

use Contao\BackendTemplate;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Grow\ActionData;
use Grow\ApplicationData;
use Grow\Organizer;

class Listing extends \Contao\Controllers\BackendMain
{

    protected $tplName = 'be_listing';

    public $ajaxActions = [
        'getList' => 'ajaxGetList',
        'getListItem' => 'ajaxGetListItem',
        'saveItem' => 'ajaxSaveItem',
        'deleteItem' => 'ajaxDeleteItem',
        'disableItem' => 'ajaxDisableItem',
        'updateForm' => 'ajaxUpdateForm'
    ];

    protected $config = [];

    protected $listTable = null;

    protected $jsAppClassName = 'Listing';

    protected $listOrganizer;

    protected $mainSectionTemplate;


    public function __construct($config = null)
    {
        $this->config = $config;

        if ($this->config['list']) {
            $this->listTable = $this->config['list']['table'];
        }

        $this->listOrganizer = new Organizer($this->listTable);

        $this->mainSectionTemplate = new BackendTemplate($this->tplName);
        $this->mainSectionTemplate->listTitle = $this->config['list']['title'];
        $this->mainSectionTemplate->listNew = $this->config['list']['labelNew'];
        $this->mainSectionTemplate->listEdit = $this->config['list']['labelEdit'];
        $this->mainSectionTemplate->listCreatable = $this->config['list']['creatable'] ? true : false;

        $GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/controllers/listing.js';

        parent::__construct();
    }


    protected function generateMainSection()
    {
        $this->Template->main = $this->mainSectionTemplate->parse();
    }


    public function ajaxGetList()
    {
        $where = is_callable($this->config['list']['whereCallback']) ? call_user_func($this->config['list']['whereCallback']) : '';
        $order = $this->config['list']['order'] ?: null;
        $join = $this->config['list']['join'] ?: '';
        $joinOn = $this->config['list']['joinOn'] ?: '';

        $headers = $this->listOrganizer->getListHeaders();
        $list = $this->listOrganizer->getList(120, 0, $where, $order, $join, $joinOn);

        $headersCallback = $this->config['list']['headersCallback'];
        $listCallback = $this->config['list']['listCallback'];

        if (is_callable($headersCallback)) {
            $headers = call_user_func($headersCallback, $headers);
        }
        if (is_callable($listCallback)) {
            $list = call_user_func($listCallback, $list);
        }

        ActionData::data('headers', $headers);
        ActionData::data('items', $list);
        ActionData::data('creatable', true);
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

        ActionData::data('fields', $fields['main']);
        if ($fields['sidebar']) {
            ActionData::data('sidebar', $fields['sidebar']);
        }
    }


    public function ajaxSaveItem()
    {
        $id = Input::post('id');
        $fields = Input::post('fields');

        if ($id === 'new') {
            $newId = $this->listOrganizer->create($fields);
            ActionData::data('newId', $newId);
        }
        else {
            $this->listOrganizer->save($id, $fields);
        }

        if ($this->listOrganizer->hasErrors()) {
            ActionData::error($this->listOrganizer->getErrors());
        }
    }


    public function ajaxDeleteItem()
    {
        $id = Input::post('id');

        $this->listOrganizer->delete($id);

        if ($this->listOrganizer->hasErrors()) {
            ActionData::error($this->listOrganizer->getErrors());
        }
    }


    public function ajaxDisableItem()
    {
        $id = Input::post('id');

        $this->listOrganizer->disable($id);

        if ($this->listOrganizer->hasErrors()) {
            ActionData::error($this->listOrganizer->getErrors());
        }
    }


    public function ajaxUpdateForm()
    {
        $id = Input::post('id');
        $fields = Input::post('fields');

        $updatedFields = $this->listOrganizer->updateForm($id, $fields);

        ActionData::data('fields', $updatedFields);
    }

}
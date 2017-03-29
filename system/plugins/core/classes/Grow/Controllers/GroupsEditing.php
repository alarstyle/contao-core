<?php

namespace Grow\Controllers;

use Contao\BackendTemplate;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Grow\ActionData;
use Grow\ApplicationData;
use Grow\Organizer;

class GroupsEditing extends \Contao\Controllers\BackendMain
{

    public $ajaxActions = [
        'getGroups' => 'ajaxGetGroups',
        'getGroupsItem' => 'ajaxGetGroupsItem',
        'saveGroup' => 'ajaxSaveGroup',
        'deleteGroup' => 'ajaxDeleteGroup'
    ];

    protected $tplName = 'be_groups_editing';

    protected $config = [];

    protected $groupTable = null;

    protected $jsAppClassName = 'GroupsEditing';

    protected $groupOrganizer;

    protected $mainTemplate;


    public function __construct($config = null)
    {
        $this->config = $config;

        if ($this->config['group']) {
            $this->groupTable = $this->config['group']['table'];
        }

        $this->groupOrganizer = new Organizer($this->groupTable);

        $GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/controllers/groups_editing.js';

        parent::__construct();
    }


    protected function generateMainSection()
    {
        $objTemplate = new BackendTemplate($this->tplName);
        $objTemplate->groupsTitle = $this->config['group']['title'];
        $objTemplate->groupsNew = $this->config['group']['labelNew'];

        $this->Template->main = $objTemplate->parse();
    }


    public function ajaxGetGroups()
    {
        $groups = [];

        $labelCallback = $this->config['group']['labelCallback'];
        $titleCallback = $this->config['group']['titleCallback'];
        $sortingCallback = $this->config['group']['sortingCallback'];

        foreach ($this->groupOrganizer->getSimpleList(30, 0) as $item) {
            $groups[] = [
                'id' => $item->id,
                'label' => is_callable($labelCallback) ? call_user_func($labelCallback, $item) : '',
                'title' => is_callable($titleCallback) ? call_user_func($titleCallback, $item) : ''
            ];
        }

        if (is_callable($sortingCallback)) {
            $groups = call_user_func($sortingCallback, $groups);
        }

        ActionData::data('groups', $groups);
        ActionData::data('creatable', true);
    }


    public function ajaxGetGroupsItem()
    {
        $id = Input::post('id');

        if ($id === 'new') {
            $fields = $this->groupOrganizer->blank();
        }
        else {
            $fields = $this->groupOrganizer->load($id);
        }

        ActionData::data('fields', $fields['main']);
        if ($fields['sidebar']) {
            ActionData::data('sidebar', $fields['sidebar']);
        }
    }


    public function ajaxSaveGroup()
    {
        $id = Input::post('id');
        $fields = Input::post('fields');

        if ($id === 'new') {
            $id = strval($this->groupOrganizer->create($fields));
            ActionData::data('newId', $id);
        }
        else {
            $this->groupOrganizer->save($id, $fields);
        }

        if ($this->groupOrganizer->hasErrors()) {
            ActionData::error($this->groupOrganizer->getErrors());
        }
    }


    public function ajaxDeleteGroup()
    {
        $id = Input::post('id');

        $this->groupOrganizer->delete($id);

        if ($this->groupOrganizer->hasErrors()) {
            ActionData::error($this->groupOrganizer->getErrors());
        }
    }

}
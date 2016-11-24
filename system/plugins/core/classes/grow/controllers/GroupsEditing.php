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

    protected $config = [];

    protected $groupTable = null;

    protected $jsFile = '/system/plugins/core/assets/js/controllers/groups_editing.js';

    protected $groupOrganizer;

    protected $mainTemplate;


    public function __construct($config = null)
    {
        $this->config = $config;

        if ($this->config['group']) {
            $this->groupTable = $this->config['group']['table'];
        }

        $this->groupOrganizer = new Organizer($this->groupTable);

        parent::__construct();
    }


    protected function generateMainSection()
    {
        $objTemplate = new BackendTemplate('be_groups_editing');
        $objTemplate->groupsTitle = $this->config['group']['title'];
        $objTemplate->groupsNew = $this->config['group']['newLabel'];

        $this->Template->main = $objTemplate->parse();
    }

    public function ajaxGetGroups()
    {
        ActionData::data('groups', $this->groupOrganizer->getList(20, 0));
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

        ActionData::data('fields', $fields);
    }


    public function ajaxSaveGroup()
    {
        $id = Input::post('id');
        $fields = Input::post('fields');

        if ($id === 'new') {
            $newId = $this->groupOrganizer->create($fields);
            ActionData::data('newId', $newId);
        }
        else {
            $this->groupOrganizer->save($id, $fields);
        }

        if ($this->groupOrganizer->hasErrors()) {
            ActionData::error($this->groupOrganizer->getErrors());
            return;
        }
    }


    public function ajaxDeleteGroup()
    {
        $id = Input::post('id');

        $result = $this->groupOrganizer->delete($id);

        if ($result !== true) {
            ActionData::error($result);
        }
    }

}
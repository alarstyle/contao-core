<?php

namespace Grow\Controllers;

use Contao\Input;
use Grow\ActionData;
use Grow\Organizer;

class ListingWithGroups extends Listing
{

    protected $groupTable = null;


    protected $groupOrganizer;


    public function __construct($config = null)
    {
        $this->ajaxActions['getGroups'] = 'ajaxGetGroups';
        $this->ajaxActions['getGroup'] = 'ajaxGetGroup';
        $this->ajaxActions['saveGroup'] = 'ajaxSaveGroup';
        $this->ajaxActions['deleteGroup'] = 'ajaxDeleteGroup';

        parent::__construct($config);

        if ($this->config['group']) {
            $this->groupTable = $this->config['group']['table'];
        }

        $this->mainSectionTemplate->groupsTitle = $this->config['group']['title'];
        $this->mainSectionTemplate->groupsNew = $this->config['group']['labelNew'];
        $this->mainSectionTemplate->groupsAll = $this->config['group']['labelAll'];

        $this->groupOrganizer = new Organizer($this->groupTable);
    }


    public function ajaxGetGroups()
    {
        $groups = [];

        $editable = $this->config['group']['editable'] ? true : false;
        $creatable = $this->config['group']['creatable'] ? true : false;
        $sortable = $this->config['group']['sortable'] ? true : false;

        $labelCallback = $this->config['group']['labelCallback'];
        $titleCallback = $this->config['group']['titleCallback'];
        $sortingCallback = $this->config['group']['sortingCallback'];

        foreach ($this->groupOrganizer->getSimpleList(20, 0) as $item) {
            $groups[] = [
                'id' => $item['id'],
                'label' => is_callable($labelCallback) ? call_user_func($labelCallback, $item) : '',
                'title' => is_callable($titleCallback) ? call_user_func($titleCallback, $item) : ''
            ];
        }

        if (is_callable($sortingCallback)) {
            $groups = call_user_func($sortingCallback, $groups);
        }

        ActionData::data('groups', $groups);
        ActionData::data('editable', $editable);
        ActionData::data('creatable', $creatable);
        ActionData::data('sortable', $sortable);
    }


    public function ajaxGetGroup()
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
<?php

namespace Gambling\Controllers;

use Contao\Controller;
use Contao\Database;
use Contao\Input;
use Contao\Session;
use Contao\System;
use Gambling\BackendHelpers;
use Gambling\ContentOrganizer;
use Grow\ActionData;
use Grow\ApplicationData;
use Grow\Controllers\GroupsEditing;
use Grow\Controllers\ListingWithGroups;
use Grow\Organizer;

class Pages extends GroupsEditing
{

    protected $tplName = 'be_pages';


    protected $jsAppClassName = 'Pages';


    protected $contentOrganizer;


    public function __construct($config)
    {
        $this->contentOrganizer = new ContentOrganizer();

        parent::__construct($config);

        $GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/gambling/assets/js/controllers/pages.js';
    }


    public function ajaxGetGroups()
    {
        $pages = [];

        foreach ($this->groupOrganizer->getSimpleList(20, 0, ["type='regular'", "pid=1", "published=1"], 'sorting') as $item) {
            $pages[] = [
                'id' => $item['id'],
                'label' => $item['title'],
                'title' => $item['title']
            ];
        }

        ActionData::data('groups', $pages);
    }


    public function ajaxGetGroupsItem()
    {
        $id = Input::post('id');

        //$fields = $this->groupOrganizer->load($id);

        $fields = $this->contentOrganizer->load($id);

        ActionData::data('fields', $fields);
    }

}
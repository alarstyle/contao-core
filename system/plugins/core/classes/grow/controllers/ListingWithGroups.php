<?php

namespace Grow\Controllers;

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
        $this->mainSectionTemplate->groupsNew = $this->config['group']['newLabel'];

        $this->groupOrganizer = new Organizer($this->groupTable);
    }


    public function ajaxGetGroups()
    {
        ActionData::data('groups', $this->groupOrganizer->getList(20, 0));
        ActionData::data('editable', true);
        ActionData::data('creatable', true);
    }


    public function ajaxGetGroup()
    {

    }


    public function ajaxSaveGroup()
    {

    }


    public function ajaxDeleteGroup()
    {

    }

}
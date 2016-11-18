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

        parent::__construct($config);

        if ($this->config['group']) {
            $this->groupTable = $this->config['group']['table'];
        }

        $this->groupOrganizer = new Organizer($this->groupTable);
    }


    public function ajaxGetGroups()
    {
        $query = "SELECT * FROM " . $this->groupTable;

        $objRowStmt = $this->Database->prepare($query);

        $objRowStmt->limit(20, 0);

        $objRow = $objRowStmt->execute();

        if ($objRow->numRows < 1) {
            return 'No data';
        }

        $result = $objRow->fetchAllAssoc();

        $groups = [];
        $listFields = $GLOBALS['TL_DCA'][$this->groupTable]['list']['label']['fields_new'];

        foreach ($result as $group) {
            $groups[] = $this->generateItemForGroup($group, $listFields);
        }

        ActionData::data('groups', $groups);
    }


    public function ajaxGetGroup()
    {

    }


    protected function generateItemForGroup($group, $listFields)
    {
        $groupData = [
            'id' => $group['id']
        ];
        foreach ($listFields as $fieldName) {
            $groupData[$fieldName] = $group[$fieldName] ?: '';
        }

        return $groupData;
    }

}
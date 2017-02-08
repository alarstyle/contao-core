<?php

namespace Gambling\Controllers;

use Contao\Input;
use Contao\Session;
use Gambling\BackendHelpers;
use Grow\ApplicationData;
use Grow\Controllers\ListingWithGroups;

class Bettings extends Casinos
{

    public function ajaxSaveGroup()
    {
        $fields = Input::post('fields');
        $fields['is_betting'] = 1;
        $fields = Input::setPost('fields', $fields);

        parent::ajaxSaveGroup();
    }


    protected function groupsWhereCallback()
    {
        return ['is_betting = 1'];
    }


    protected function listWhereCallback()
    {
        $this->where[] = 'countries LIKE \'%"' . $this->currentCountryId . '"%\' OR countries = "a:0:{}"';
        $this->where[] = 'is_betting = 1';
        $groupId = Input::post('groupId');
        if (!empty($groupId)) {
            $this->where[] = 'betting_categories LIKE \'%"' . $groupId . '"%\'';
        }
        return $this->where;
    }

}
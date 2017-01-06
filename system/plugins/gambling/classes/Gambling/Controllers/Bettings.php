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
        $fields['isBetting'] = 1;
        $fields = Input::setPost('fields', $fields);

        parent::ajaxSaveGroup();
    }


    protected function groupsWhereCallback()
    {
        return ['isBetting = 1'];
    }


    protected function listWhereCallback()
    {
        $this->where[] = 'countries LIKE \'%"' . $this->currentCountryId . '"%\' OR countries = "a:0:{}"';
        $this->where[] = 'isBetting = 1';
        $groupId = Input::post('groupId');
        if (!empty($groupId)) {
            $this->where[] = 'betting_categories LIKE \'%"' . $groupId . '"%\'';
        }
        return $this->where;
    }

}
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
        $this->listOrganizer->listQuery
            ->startGroup()
                ->where('countries', 'like', '%"' . $this->currentCountryId . '"%')
                ->orWhere('countries', 'a:0:{}')
                ->orWhere('countries', '')
            ->endGroup()
            ->where('is_betting', 1);

        $groupId = Input::post('groupId');
        if (!empty($groupId)) {
            $this->listOrganizer->listQuery
                ->join('tl_casino_data', 'data', 'left')
                    ->on('tl_casino.id', 'data.pid')
                ->where('data.country', $this->currentCountryId)
                ->where('data.betting_categories', 'like', '%"' . $groupId . '"%');
        }

        return $this->where;
    }

}
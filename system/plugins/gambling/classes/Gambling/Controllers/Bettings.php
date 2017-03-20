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
            ->fields(['*'])
            ->fields('tl_casino.id', 'id')
            ->join('tl_casino_data', 'data', 'left')
            ->on('tl_casino.id', 'data.pid')
            ->where('data.country', $this->currentCountryId)
            ->startGroup()
                ->where('countries', 'like', '%"' . $this->currentCountryId . '"%')
                ->orWhere('countries', 'a:0:{}')
                ->orWhere('countries', '')
            ->endGroup()
            ->where('is_betting', 1);

        $groupId = Input::post('groupId');
        if (!empty($groupId)) {
            $this->listOrganizer->listQuery
                ->where('data.betting_categories', 'like', '%"' . $groupId . '"%');
        }

        return $this->where;
    }

    protected function setListNewPosition($id, $previousId, $countryId)
    {
        $connection = \Grow\Database::getConnection();

        $newSorting = $this->getNewPosition('tl_casino_data', 'pid', 'betting_sorting', $previousId, [$this, 'modifyListQuery']);

        $connection->updateQuery()
            ->table('tl_casino_data')
            ->set('betting_sorting', $newSorting)
            ->where('pid', $id)
            ->where('country', $this->currentCountryId)
            ->execute();
    }

    protected function modifyGroupQuery($query)
    {
        $query->where('is_betting', 1);
    }

}
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


    protected function listWhereCallback()
    {
        $database = \Grow\Database::getDatabase();
        $expression = $database->sqlExpression('SELECT * from tl_casino_data WHERE country = ?', [$this->currentCountryId]);
        $this->listOrganizer->listQuery
            ->fields(['*'])
            ->fields('tl_casino.id', 'id')
            ->join($expression, 'data', 'left')
            ->on('tl_casino.id', 'data.pid')
            ->where('is_betting', 1)
            ->startGroup()
            ->where('countries', 'like', '%"' . $this->currentCountryId . '"%')
            ->orWhere('countries', 'a:0:{}')
            ->orWhere('countries', '')
            ->endGroup();
//        $this->listOrganizer->listQuery
//            ->fields(['*'])
//            ->fields('tl_casino.id', 'id')
//            ->join('tl_casino_data', 'data', 'left')
//            ->on('tl_casino.id', 'data.pid')
//            ->where('data.country', $this->currentCountryId)
//            ->startGroup()
//                ->where('countries', 'like', '%"' . $this->currentCountryId . '"%')
//                ->orWhere('countries', 'a:0:{}')
//                ->orWhere('countries', '')
//            ->endGroup()
//            ->where('is_betting', 1);

        $groupId = intval(Input::post('groupId'));
        if (!empty($groupId)) {
            $this->listOrganizer->listQuery
                ->where('data.betting_categories', 'like', '%"' . $groupId . '"%');
        }

        return $this->where;
    }

    public function groupsHook($query)
    {
        $query
            ->fields(['*'])
            ->fields('tl_casino_category.id', 'id')
            ->join('tl_casino_category_data', 'data', 'left')
            ->on('tl_casino_category.id', 'pid')
            ->where('is_betting', 1)
            ->where('data.country', $this->currentCountryId)
            ->orderBy('sorting', 'desc');
            //->orderBy('tstamp', 'asc');
    }

    protected function setListNewPosition($id, $previousId, $countryId)
    {
        $connection = \Grow\Database::getConnection();

        $newSorting = $this->getNewPosition('tl_casino_data', 'id', 'pid', 'betting_sorting', $previousId, [$this, 'modifyListQuery']);

        $connection->updateQuery()
            ->table('tl_casino_data')
            ->set('betting_sorting', $newSorting)
            ->where('pid', $id)
            ->where('country', $this->currentCountryId)
            ->execute();
    }

    protected function modifyGroupQuery($query)
    {
        $query
            ->fields('tl_casino_category_data.id', 'id')
            ->fields('tl_casino_category_data.pid', 'pid')
            ->fields('tl_casino_category_data.sorting', 'sorting')
            ->fields('tl_casino_category_data.country', 'country')
            ->join('tl_casino_category', 'tl_casino_category', 'left')
            ->on('tl_casino_category.id', 'pid')
            ->where('country', $this->currentCountryId)
            ->where('tl_casino_category.is_betting', 1)
        ;
    }

}
<?php

namespace Gambling\Controllers;

use Contao\Database;
use Contao\Input;
use Contao\Session;
use Gambling\BackendHelpers;
use Grow\ActionData;
use Grow\ApplicationData;
use Grow\Controllers\ListingWithGroups;
use Grow\Organizer;

class Casinos extends ListingWithGroups
{

    protected $tplName = 'be_casinos';


    protected $jsAppClassName = 'Casinos';


    protected $where = [];


    protected $session;


    protected $currentCountryId;


    protected $casinoDataOrganizer;


    public function __construct($config)
    {
        $this->ajaxActions['changeCountry'] = 'ajaxChangeCountry';
        $this->ajaxActions['getCasinoData'] = 'ajaxGetCasinoData';
        $this->ajaxActions['saveCasinoData'] = 'ajaxSaveCasinoData';
        $this->ajaxActions['reorderGroups'] = 'ajaxReorderGroups';
        $this->ajaxActions['reorderList'] = 'ajaxReorderList';

        parent::__construct($config);

        $GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/gambling/assets/js/controllers/casinos.js';

        $this->session = Session::getInstance();
        $this->currentCountryId = $this->session->get('CurrentCountry');

        $this->casinoDataOrganizer = new Organizer('tl_casino_data');

        $countries = BackendHelpers::getUserAvailableCountriesForOptions();
        $availableCountries = [];
        foreach ($countries as $key => $value) {
            $availableCountries[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        if (empty($availableCountries)) {
            throw new \Exception('User has no countries to access');
        }

        if (empty($this->currentCountryId) || !in_array($this->currentCountryId, array_keys($countries))) {
            $this->currentCountryId = $availableCountries[0]['value'];
            $this->session->set('CurrentCountry', $this->currentCountryId);
        }

        ApplicationData::addData('availableCountries', $availableCountries);
        ApplicationData::addData('currentCountry', $this->currentCountryId);
    }


    public function ajaxGetGroups()
    {
        $this->config['group']['whereCallback'] = [$this, 'groupsWhereCallback'];
        $this->config['group']['labelCallback'] = [$this, 'groupsLabelCallback'];
        $this->config['group']['titleCallback'] = [$this, 'groupsLabelCallback'];
        parent::ajaxGetGroups();
    }


    public function ajaxGetList()
    {
        $this->config['list']['whereCallback'] = [$this, 'listWhereCallback'];

        parent::ajaxGetList();
    }


    public function ajaxChangeCountry()
    {
        $countryId = intval(Input::post('countryId'));
        $this->session->set('CurrentCountry', $countryId);
    }


    public function ajaxGetCasinoData()
    {
        $id = Input::post('id');
        $fieldsValues = Input::post('fields');
        $casinoCountries = $fieldsValues['countries'] ?: [];

        $casinoData = [];

        $objRow = Database::getInstance()->prepare("SELECT id, country FROM tl_casino_data WHERE pid=?")
            ->execute($id);

        if ($objRow->numRows >= 1) {
            foreach ($objRow->fetchAllAssoc() as $row) {
                $casinoData[$row['country']] = $this->casinoDataOrganizer->load($row['id']);
            }
        }

        foreach ($casinoCountries as $countryId) {
            if (isset($casinoData[$countryId])) continue;
            $casinoData[$countryId] = $this->casinoDataOrganizer->blank();
        }

        ActionData::data('casinoData', $casinoData);
    }


    public function ajaxSaveCasinoData()
    {
        $id = Input::post('id');
        $countryId = Input::post('countryId');
        $fields = Input::post('fields');

        $objRow = Database::getInstance()->prepare("SELECT id FROM tl_casino_data WHERE pid=? AND country=?")
            ->limit(1)
            ->execute($id, $countryId);

        if ($objRow->numRows < 1) {
            $fields['country'] = $countryId;
            $fields['pid'] = $id;
            $dataId = $this->casinoDataOrganizer->create($fields);
        } else {
            $dataId = $objRow->id;
            $this->casinoDataOrganizer->save($dataId, $fields);
        }

        if ($this->casinoDataOrganizer->hasErrors()) {
            ActionData::error($this->casinoDataOrganizer->getErrors());
            return;
        }

        $casinoData = $this->casinoDataOrganizer->load($dataId);
        ActionData::data('casinoData', $casinoData);
    }


    public function ajaxReorderGroups()
    {
        $id = Input::post('id');
        $previousId = Input::post('previousId');

        $this->setGroupNewPosition($id, $previousId);
    }


    public function ajaxReorderList()
    {
        $id = Input::post('id');
        $previousId = Input::post('previousId');

        $this->setListNewPosition($id, $previousId);
    }


    protected function groupsLabelCallback($item)
    {
        $defaultCountry = BackendHelpers::getDefaultCountry();
        $nameArr = deserialize($item['name']);
        $name = $nameArr[$defaultCountry['id']];
        return $name ?: $item['alias'];
    }

    protected function groupsWhereCallback()
    {
        return ['NOT is_betting = 1'];
    }


    protected function listWhereCallback()
    {
        $this->listOrganizer->listQuery
            ->startGroup()
                ->where('countries', 'like', '%"' . $this->currentCountryId . '"%')
                ->orWhere('countries', 'a:0:{}')
                ->orWhere('countries', '')
            ->endGroup()
            ->where('is_casino', 1);

        $groupId = Input::post('groupId');
        if (!empty($groupId)) {
            $this->listOrganizer->listQuery
                ->join('tl_casino_data', 'data', 'left')
                    ->on('tl_casino.id', 'data.pid')
                ->where('data.country', $this->currentCountryId)
                ->where('data.casino_categories', 'like', '%"' . $groupId . '"%');
        }

        return $this->where;
    }


    protected function setGroupNewPosition($id, $previousId)
    {
        $connection = \Grow\Database::getConnection();

        $newSorting = $this->getNewPosition('tl_casino_category', $id, $previousId, [$this, 'modifyGroupQuery']);

        $connection->updateQuery()
            ->table('tl_casino_category')
            ->set('sorting', $newSorting)
            ->where('id', $id)
            ->execute();
    }


    protected function setListNewPosition($id, $previousId)
    {
        $connection = \Grow\Database::getConnection();

//        $newSorting = $this->getNewPosition('tl_casino_data', $id, $previousId, [$this, 'modifyGroupQuery']);
//
//        $connection->updateQuery()
//            ->table('tl_casino_category')
//            ->set('sorting', $newSorting)
//            ->where('id', $id)
//            ->execute();
    }


    protected function getNewPosition($table, $id, $previousId, callable $modifyQueryMethod = null)
    {
        $newSorting = 0;

        $database = \Grow\Database::getDatabase();
        $connection = \Grow\Database::getConnection();

        if (!$previousId) {
            // ID is not set (insert at the end)
            $maxSortingQuery = $connection->selectQuery()
                ->table($table)
                ->fields(['sorting' => $database->sqlExpression('MAX(sorting)')]);
            if ($modifyQueryMethod) $modifyQueryMethod($maxSortingQuery);
            $maxSorting = $maxSortingQuery->execute()->asArray();
            $maxSorting = $maxSorting && $maxSorting[0] ? $maxSorting[0]->sorting : 0;
            $newSorting = intval($maxSorting) + 128;

            return $newSorting;
        }

        $previous = $connection->selectQuery()
            ->table($table)
            ->where('id', $previousId)
            ->execute()->asArray();

        $prevSorting = !count($previous) ? 0 : $previous[0]->sorting;

        ActionData::data('previousSorting', $prevSorting);

        $nextQuery = $connection->selectQuery()
            ->table($table)
            ->fields(['sorting' => $database->sqlExpression('MIN(sorting)')])
            ->where('sorting', '<', $prevSorting);
        if ($modifyQueryMethod) $modifyQueryMethod($nextQuery);
        $next = $nextQuery->execute()->asArray();

//        if (!count($next)) {
//            return $prevSorting + 128;
//        }

        $nextSorting = !count($next) ? 0 : $next[0]->sorting;

        if ($prevSorting !== 0 && (($prevSorting + $nextSorting) % 2) === 0 && $nextSorting < 4294967295)
        {
            return ($prevSorting + $nextSorting) / 2;
        }

        $count = 1;

        $itemsQuery = $connection->selectQuery()
            ->table($table)
            ->fields(['id', 'sorting'])
            ->orderBy('sorting', 'asc');
        if ($modifyQueryMethod) $modifyQueryMethod($itemsQuery);
        $items = $itemsQuery->execute()->asArray();

        foreach ($items as $item) {
            if ($item->id == $previousId)
            {
                $newSorting = ($count++ * 128);
            }
            $connection->updateQuery()
                ->table($table)
                ->set('sorting', ($count++ * 128))
                ->where('id', $item->id)
                ->execute();
        }

        return $newSorting;
    }

    protected function modifyGroupQuery($query)
    {
        $query->where('is_betting', '!=', 1);
    }

}
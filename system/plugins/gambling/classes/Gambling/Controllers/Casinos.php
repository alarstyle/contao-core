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
        // TEMP FIX
//        $connection = \Grow\Database::getConnection();
//        $countries = BackendHelpers::getCountriesForOptions();
//        $categories = $connection->selectQuery()
//            ->table('tl_casino_category')
//            ->fields('id')
//            ->execute()->asArray();
//
//        foreach ($categories as $category) {
//            $categoryDataArr = $connection->selectQuery()
//                ->table('tl_casino_category_data')
//                ->where('pid', $category->id)
//                ->execute()->asArray();
//            $missingCountries = array_keys($countries);
//            foreach ($categoryDataArr as $categoryData) {
//                if(($i = array_search($categoryData->country, $missingCountries)) !== false) {
//                    unset($missingCountries[$i]);
//                }
//            }
//            foreach ($missingCountries as $countryId) {
//                $connection->insertQuery()
//                    ->table('tl_casino_category_data')
//                    ->data([
//                        'pid' => $category->id,
//                        'country' => $countryId,
//                        'sorting' => 0
//                    ])
//                    ->execute();
//            }
//        }

        $this->config['group']['labelCallback'] = [$this, 'groupsLabelCallback'];
        $this->config['group']['titleCallback'] = [$this, 'groupsLabelCallback'];
        $this->config['group']['hook'] = [$this, 'groupsHook'];
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
        $id = intval(Input::post('id'));
        $fieldsValues = Input::post('fields');
        $casinoCountries = $fieldsValues['countries'] ?: [];

        $casinoData = [];

        $objRow = Database::getInstance()->prepare("SELECT id, country FROM tl_casino_data WHERE pid=?")
            ->execute($id);

        if ($objRow->numRows >= 1) {
            foreach ($objRow->fetchAllAssoc() as $row) {
                $casinoData[$row['country']] = $this->loadCasinoData($row['id'], $row['country']);
            }
        }

        foreach ($casinoCountries as $countryId) {
            if (isset($casinoData[$countryId])) continue;
            $casinoData[$countryId] = $this->casinoDataOrganizer->blank();
        }

        ActionData::data('casinoData', $casinoData);
    }


    public function ajaxSaveGroup()
    {
        $id = intval(Input::post('id'));

        parent::ajaxSaveGroup();

        if ($id === 'new' && !$this->groupOrganizer->hasErrors()) {
            $countries = BackendHelpers::getCountriesForOptions();
            $connection = \Grow\Database::getConnection();
            $newId = ActionData::getData('newId');
            foreach ($countries as $countryId=>$countryName) {
                $connection->insertQuery()
                    ->table('tl_casino_category_data')
                    ->data([
                        'pid' => $newId,
                        'country' => $countryId,
                        'sorting' => 0
                    ])
                    ->execute();
            }
        }
    }


    public function ajaxSaveCasinoData()
    {
        $id = intval(Input::post('id'));
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

        $casinoData = $this->loadCasinoData($dataId, $countryId);
        ActionData::data('casinoData', $casinoData);
    }


    public function ajaxReorderGroups()
    {
        $id = intval(Input::post('id'));
        $previousId = intval(Input::post('previousId'));
        $countryId = intval(Input::post('countryId'));

        $this->setGroupNewPosition($id, $previousId, $countryId);
    }


    public function ajaxReorderList()
    {
        $id = intval(Input::post('id'));
        $previousId = intval(Input::post('previousId'));
        $countryId = intval(Input::post('countryId'));

        $this->setListNewPosition($id, $previousId, $countryId);
    }


    protected function groupsLabelCallback($item)
    {
        $defaultCountry = BackendHelpers::getDefaultCountry();
        $nameArr = deserialize($item->name);
        $name = $nameArr[$defaultCountry['id']];
        return $name ?: $item->id;
    }


    protected function listWhereCallback()
    {
        $database = \Grow\Database::getDatabase();
        $expression = $database->sqlExpression('SELECT * from tl_casino_data WHERE country = ' . $this->currentCountryId);
        $this->listOrganizer->listQuery
            ->fields(['*'])
            ->fields('tl_casino.id', 'id')
            ->join($expression, 'data', 'left')
            ->on('tl_casino.id', 'data.pid')
            ->where('is_casino', 1)
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
//            ->where('is_casino', 1);

        $groupId = intval(Input::post('groupId'));
        if (!empty($groupId)) {
            $this->listOrganizer->listQuery
                ->where('data.casino_categories', 'like', '%"' . $groupId . '"%');
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
            ->where('is_betting', '!=', 1)
            ->where('data.country', $this->currentCountryId)
            ->orderBy('sorting', 'desc');
            //->orderBy('tstamp', 'asc');
    }


    protected function setGroupNewPosition($id, $previousId, $countryId)
    {
        $connection = \Grow\Database::getConnection();

        $newSorting = $this->getNewPosition('tl_casino_category_data', 'tl_casino_category_data.id', 'pid', 'tl_casino_category_data.sorting', $previousId, [$this, 'modifyGroupQuery']);

        $categoryData = $connection->selectQuery()
            ->table('tl_casino_category_data')
            ->where('pid', $id)
            ->where('country', $countryId)
            ->execute()->asArray();

        if (!count($categoryData)) {
            $connection->insertQuery()
                ->table('tl_casino_category_data')
                ->data([
                    'pid' => $id,
                    'country' => $countryId,
                    'sorting' => $newSorting
                ])
                ->execute();
        }
        else {
            $connection->updateQuery()
                ->table('tl_casino_category_data')
                ->set('sorting', $newSorting)
                ->where('pid', $id)
                ->where('country', $countryId)
                ->execute();
        }
    }


    protected function setListNewPosition($id, $previousId, $countryId)
    {
        $connection = \Grow\Database::getConnection();

        $newSorting = $this->getNewPosition('tl_casino_data', 'id', 'pid', 'casino_sorting', $previousId, [$this, 'modifyListQuery']);

        $connection->updateQuery()
            ->table('tl_casino_data')
            ->set('casino_sorting', $newSorting)
            ->where('pid', $id)
            ->where('country', $countryId)
            ->execute();
    }


    protected function getNewPosition($table, $rowIdFieldName, $idFiledName, $sortingFieldName, $previousId, callable $modifyQueryMethod = null)
    {
        $newSorting = 0;

        $database = \Grow\Database::getDatabase();
        $connection = \Grow\Database::getConnection();

        if (!$previousId) {
            // ID is not set (insert at the end)
            $maxSortingQuery = $connection->selectQuery()
                ->table($table)
                ->fields([$sortingFieldName => $database->sqlExpression('MAX('.$sortingFieldName.')')]);
            if ($modifyQueryMethod) $modifyQueryMethod($maxSortingQuery);
            $maxSorting = $maxSortingQuery->execute()->asArray();
            $maxSorting = $maxSorting && $maxSorting[0] ? $maxSorting[0]->$sortingFieldName : 0;
            $newSorting = intval($maxSorting) + 128;

            return $newSorting;
        }

        $previousQuery = $connection->selectQuery()
            ->table($table)
            ->where($idFiledName, $previousId);
        if ($modifyQueryMethod) $modifyQueryMethod($previousQuery);
        $previous = $previousQuery->execute()->asArray();

        $prevSorting = !count($previous) ? 0 : intval($previous[0]->$sortingFieldName);

        ActionData::data('previousSorting', $prevSorting);

        $nextQuery = $connection->selectQuery()
            ->table($table)
            ->fields([$sortingFieldName => $database->sqlExpression('MAX('.$sortingFieldName.')')])
            ->where($sortingFieldName, '<', $prevSorting);
        if ($modifyQueryMethod) $modifyQueryMethod($nextQuery);
        $next = $nextQuery->execute()->asArray();

        $nextSorting = !count($next) ? 0 : intval($next[0]->$sortingFieldName);

        ActionData::data('nextSorting', $nextSorting);

        if ($prevSorting !== 0 && (($prevSorting + $nextSorting) % 2) === 0 && $nextSorting < 4294967295)
        {
            ActionData::data('new pos', ($prevSorting + $nextSorting) / 2);
            return ($prevSorting + $nextSorting) / 2;
        }

        ActionData::data('reordering', true);

        $count = 1;

        $itemsQuery = $connection->selectQuery()
            ->table($table)
            ->fields([$rowIdFieldName, $idFiledName, $sortingFieldName])
            ->orderBy($sortingFieldName, 'asc');
        if ($modifyQueryMethod) $modifyQueryMethod($itemsQuery);
        $items = $itemsQuery->execute()->asArray();

        foreach ($items as $item) {
            if ($item->$idFiledName == $previousId)
            {
                $newSorting = ($count++ * 128);
            }
            $connection->updateQuery()
                ->table($table)
                ->set($sortingFieldName, ($count++ * 128))
                ->where($rowIdFieldName, $item->id)
                ->execute();
        }

        return $newSorting;
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
            ->where('tl_casino_category.is_betting', '!=', 1)
        ;
    }

    protected function modifyListQuery($query)
    {
        $query->where('country', $this->currentCountryId);
    }


    protected function loadCasinoData($id, $countryId)
    {
        $data = $this->casinoDataOrganizer->load($id);
//        $data['main']['wagering_casino']['config']['options'] = \Gambling\BackendHelpers::getCasinoOptions('wagering requirement', $countryId);
//        $data['main']['wagering_betting']['config']['options'] = \Gambling\BackendHelpers::getCasinoOptions('wagering requirement', $countryId);
        $data['main']['withdrawal_methods']['config']['options'] = \Gambling\BackendHelpers::getCasinoOptions('withdrawal methods', $countryId);
        $data['main']['deposit_methods']['config']['options'] = \Gambling\BackendHelpers::getCasinoOptions('deposit methods', $countryId);
        $data['main']['providers']['config']['options'] = \Gambling\BackendHelpers::getCasinoOptions('game providers', $countryId);
        $data['main']['licenses']['config']['options'] = \Gambling\BackendHelpers::getCasinoOptions('licenses', $countryId);

        return $data;
    }

}
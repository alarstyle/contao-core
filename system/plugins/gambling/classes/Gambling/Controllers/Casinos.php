<?php

namespace Gambling\Controllers;

use Contao\Input;
use Contao\Session;
use Gambling\BackendHelpers;
use Grow\ApplicationData;
use Grow\Controllers\ListingWithGroups;

class Casinos extends ListingWithGroups
{

    protected $tplName = 'be_casinos';


    protected $jsAppClassName = 'Casinos';


    protected $where = [];


    protected $session;


    protected $currentCountryId;


    public function __construct($config)
    {
        $this->ajaxActions['changeCountry'] = 'ajaxChangeCountry';

        parent::__construct($config);

        $GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/gambling/assets/js/controllers/casinos.js';

        $this->session = Session::getInstance();
        $this->currentCountryId = $this->session->get('postsCurrentCountry');

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
            $this->session->set('postsCurrentCountry', $this->currentCountryId);
        }

        ApplicationData::addData('availableCountries', $availableCountries);
        ApplicationData::addData('currentCountry', $this->currentCountryId);
    }


    public function ajaxGetGroups()
    {
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
        $this->session->set('postsCurrentCountry', $countryId);
    }


    protected function groupsLabelCallback($item)
    {
        $defaultCountry = BackendHelpers::getDefaultCountry();
        $nameArr = deserialize($item['name']);
        $name = $nameArr[$defaultCountry['id']];
        return $name ?: $item['alias'];
    }


    protected function listWhereCallback()
    {
        $this->where[] = 'countries LIKE \'%"' . $this->currentCountryId . '"%\' OR countries = "a:0:{}"';
        return $this->where;
    }

}
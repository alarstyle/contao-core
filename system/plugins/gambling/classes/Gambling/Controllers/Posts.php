<?php

namespace Gambling\Controllers;

use Contao\Input;
use Contao\Session;
use Gambling\BackendHelpers;
use Gambling\Gambling;
use Grow\ApplicationData;
use Grow\Controllers\ListingWithGroups;

class Posts extends ListingWithGroups
{

    protected $tplName = 'be_posts';


    protected $jsAppClassName = 'Posts';


    protected $where = [];


    protected $session;


    protected $currentCountry;


    public function __construct($config)
    {
        $this->ajaxActions['changeCountry'] = 'ajaxChangeCountry';

        parent::__construct($config);

        $GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/gambling/assets/js/controllers/posts.js';

        $this->session = Session::getInstance();
        $this->currentCountry = $this->session->get('CurrentCountry');

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

        if (empty($this->currentCountry) || !in_array($this->currentCountry, array_keys($countries))) {
            $this->currentCountry = $availableCountries[0]['value'];
            $this->session->set('CurrentCountry', $this->currentCountry);
        }

        ApplicationData::addData('availableCountries', $availableCountries);
        ApplicationData::addData('currentCountry', $this->currentCountry);
    }


    public function ajaxChangeCountry()
    {
        $countryId = intval(Input::post('countryId'));
        $this->session->set('CurrentCountry', $countryId);
    }


    public function ajaxGetList()
    {
        $where = is_callable($this->config['list']['whereCallback']) ? call_user_func($this->config['list']['whereCallback']) : '';

        if (!empty($where)) {
            $this->where[] = $where;
        }

        $this->config['list']['whereCallback'] = [$this, 'listWhereCallback'];

        parent::ajaxGetList();
    }


    public function ajaxSaveItem()
    {
        $fields = Input::post('fields');
        $fields['country'] = $this->currentCountry;
        Input::setPost('fields', $fields);
        parent::ajaxSaveItem();
    }


    public function ajaxGetGroups()
    {
        $this->config['group']['labelCallback'] = [$this, 'groupsLabelCallback'];
        $this->config['group']['titleCallback'] = [$this, 'groupsLabelCallback'];
        parent::ajaxGetGroups();
    }


    public function ajaxSaveGroup()
    {
        $fields = Input::post('fields');
        $fields['country'] = $this->currentCountry;
        Input::setPost('fields', $fields);
        parent::ajaxSaveGroup();
    }


    protected function listWhereCallback()
    {
        $this->where[] = 'country = ' . $this->currentCountry;
        $this->listOrganizer->listQuery
            ->where('country', $this->currentCountry);

        return $this->where;
    }


    protected function groupsLabelCallback($item)
    {
        $nameArr = deserialize($item['name']);
        $name = $nameArr[$this->currentCountry];
        return $name ?: $item['alias'];
    }

}
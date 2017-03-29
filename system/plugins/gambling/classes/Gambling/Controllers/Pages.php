<?php

namespace Gambling\Controllers;

use Contao\Controller;
use Contao\Database;
use Contao\Input;
use Contao\Session;
use Contao\System;
use Gambling\BackendHelpers;
use Gambling\ContentOrganizer;
use Grow\ActionData;
use Grow\ApplicationData;
use Grow\Controllers\GroupsEditing;
use Grow\Controllers\ListingWithGroups;
use Grow\Organizer;

class Pages extends GroupsEditing
{

    protected $tplName = 'be_pages';


    protected $jsAppClassName = 'Pages';


    protected $contentOrganizer;


    protected $session;


    protected $currentCountryId;


    public function __construct($config)
    {
        $this->ajaxActions['changeCountry'] = 'ajaxChangeCountry';

        $this->contentOrganizer = new ContentOrganizer();

        parent::__construct($config);

        $GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/gambling/assets/js/controllers/pages.js';

        $this->session = Session::getInstance();
        $this->currentCountryId = $this->session->get('CurrentCountry');

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


    public function ajaxChangeCountry()
    {
        $countryId = intval(Input::post('countryId'));
        $pageId = intval(Input::post('id'));
        $this->session->set('CurrentCountry', $countryId);

        if (empty($pageId)) return;

        $this->ajaxGetGroupsItem();
    }


    public function ajaxGetGroups()
    {
        $pages = [];

        foreach ($this->groupOrganizer->getSimpleList(30, 0, [['type', 'regular'], ['pid', 1], ['published', 1]], [['sorting', 'asc']]) as $item) {
            $pages[] = [
                'id' => $item->id,
                'label' => $item->title,
                'title' => $item->title
            ];
        }

        ActionData::data('groups', $pages);
    }


    public function ajaxGetGroupsItem()
    {
        $id = Input::post('id');

        $fields = $this->contentOrganizer->load($id);

        ActionData::data('fields', $fields);
    }


    public function ajaxSaveGroup()
    {
        $id = Input::post('id');
        $fields = Input::post('fields');

        $this->contentOrganizer->save($id, $fields);

        if ($this->contentOrganizer->hasErrors()) {
            ActionData::error($this->groupOrganizer->getErrors());
        }
    }

}
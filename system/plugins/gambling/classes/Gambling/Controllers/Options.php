<?php

namespace Gambling\Controllers;

use Contao\BackendTemplate;
use Contao\Input;
use Contao\Session;
use Gambling\BackendHelpers;
use Gambling\OptionsOrganizer;
use Grow\ActionData;
use Grow\ApplicationData;

class Options extends \Contao\Controllers\BackendMain
{

    protected $tplName = 'be_options';

    public $ajaxActions = [
        'loadVariables' => 'ajaxLoadVariables',
        'saveVariables' => 'ajaxSaveVariables',
        'changeCountry' => 'ajaxChangeCountry'
    ];

    protected $jsAppClassName = 'Translations';

    protected $session;

    protected $currentCountry;

    protected $variablesOrganizer;

    protected $mainSectionTemplate;


    protected $frontendVariables = [
//        'wagering requirement',
        'withdrawal methods',
        'deposit methods',
        'game providers',
        'licenses'
    ];


    public function __construct($config)
    {
        $this->config = $config;

        $this->variablesOrganizer = new OptionsOrganizer();

        $this->mainSectionTemplate = new BackendTemplate($this->tplName);

        parent::__construct();

        $GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/gambling/assets/js/controllers/translations.js';

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


    protected function generateMainSection()
    {
        $this->Template->main = $this->mainSectionTemplate->parse();
    }


    public function ajaxChangeCountry()
    {
        $countryId = intval(Input::post('countryId'));
        $this->session->set('CurrentCountry', $countryId);
    }


    public function ajaxLoadVariables()
    {
        $countryId = Input::post('countryId');

        $fields = $this->variablesOrganizer->loadVariables($countryId, $this->frontendVariables);

        ActionData::data('fields', $fields);
    }


    public function ajaxSaveVariables()
    {
        $countryId = Input::post('countryId');
        $fields = Input::post('fields');

        $fields = $this->variablesOrganizer->saveVariables($countryId, $fields);
    }

}
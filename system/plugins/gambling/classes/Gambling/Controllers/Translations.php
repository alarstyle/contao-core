<?php

namespace Gambling\Controllers;

use Contao\BackendTemplate;
use Contao\Input;
use Contao\Session;
use Gambling\BackendHelpers;
use Gambling\VariablesOrganizer;
use Grow\ActionData;
use Grow\ApplicationData;

class Translations extends \Contao\Controllers\BackendMain
{

    protected $tplName = 'be_translations';

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
        'about deal',
        'all articles',
        'bettings',
        'bonuses',
        'by',
        'casino review',
        'casino bonus',
        'code',
        'company information',
        'contact info',
        'copyright',
        'deposit bonus',
        'deposit methods',
        'email',
        'established',
        'explore more',
        'free bet',
        'free spins',
        'game providers',
        'games',
        'go black',
        'hottest promotions',
        'languages',
        'latest news',
        'learn more',
        'licenses',
        'make bet',
        'max. withdrawal',
        'message',
        'min. withdrawal',
        'name',
        'newest casinos',
        'newest betting',
        'news',
        'newsletter subscribe',
        'overview',
        'owner',
        'page not found',
        'play now',
        'read more',
        'reviews',
        'see all promo',
        'send message',
        'send us a message',
        'show',
        'show more',
        'sign up bonus',
        'subscribe',
        'support mail',
        'support phone',
        'till the end of deal',
        'top 5 casinos',
        'top 5 betting',
        'up to',
        'visit',
        'wagering requirement',
        'website',
        'withdrawal methods',
        'your email'
    ];


    public function __construct($config)
    {
        $this->config = $config;

        $this->variablesOrganizer = new VariablesOrganizer();

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
        $category = Input::post('category');
        $countryId = Input::post('countryId');

        $fields = $this->variablesOrganizer->loadVariables($category, $countryId, $this->frontendVariables);

        ActionData::data('fields', $fields);
    }


    public function ajaxSaveVariables()
    {
        $category = Input::post('category');
        $countryId = Input::post('countryId');
        $fields = Input::post('fields');

        $fields = $this->variablesOrganizer->saveVariables($category, $countryId, $fields);
    }

}
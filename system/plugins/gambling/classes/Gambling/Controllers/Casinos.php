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


    public function __construct($config)
    {
        parent::__construct($config);

        $GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/gambling/assets/js/controllers/casinos.js';
    }

}
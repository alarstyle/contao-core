<?php

namespace Grow\Controllers;

class Dashboard extends \Contao\Controllers\BackendMain
{

    protected function generateMainSection()
    {
        $this->Template->main = 'Dashboard will be enabled when everything else is ready';
    }

}
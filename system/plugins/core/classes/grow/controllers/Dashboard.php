<?php

namespace Grow\Controllers;

class Dashboard extends \Contao\Controllers\BackendMain
{

    protected function generateMainSection()
    {
        $this->Template->main = 'ok';
    }

}
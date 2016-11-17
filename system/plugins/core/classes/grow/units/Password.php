<?php

namespace Grow\Units;

use Contao\Encryption;

class Password extends AbstractUnit
{

    public static $componentName = 'unit-password';


    public function getUnitData($value)
    {
        return parent::getUnitData($value !== '' ? '******' : '');
    }


    protected function validator($value)
    {
        $value = parent::validator($value);

        if (!$this->hasErrors()) {
            return Encryption::hash($value);
        }

        return '';
    }

}
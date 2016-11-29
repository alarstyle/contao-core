<?php

namespace Grow\Units;

use Contao\Encryption;

class Password extends AbstractUnit
{

    public static $componentName = 'unit-password';


    public function getUnitData($value)
    {
        return parent::getUnitData(!empty($value) ? '*****' : '');
    }


    protected function validator($value, $id = null)
    {
        $value = parent::validator($value);

        if ($value === '*****') {
            $this->skip = true;
            return true;
        }

        if (!$this->hasErrors()) {
            return Encryption::hash($value);
        }

        return '';
    }

}
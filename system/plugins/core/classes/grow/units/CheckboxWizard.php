<?php

namespace Grow\Units;

class CheckboxWizard extends AbstractUnit
{

    public static $componentName = 'unit-checkbox-wizard';


    protected function validator($value)
    {
        $value = parent::validator($value);

        if (is_array($value)) {
            $value = serialize($value);
        }

        return $value;
    }

}
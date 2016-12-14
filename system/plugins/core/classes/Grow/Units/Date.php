<?php

namespace Grow\Units;

class Date extends AbstractUnit
{

    public static $componentName = 'unit-date';


    public function getUnitData($value)
    {
        $unitData = parent::getUnitData($value);

        if (empty($unitData['value'])) {
            $unitData['value'] = '';
        }

        return $unitData;
    }


}
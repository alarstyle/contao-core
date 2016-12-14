<?php

namespace Gambling\Units;

use Gambling\Gambling;
use Grow\Units\AbstractUnit;

class MultilingualText extends AbstractUnit
{

    public static $componentName = 'unit-multilingual-text';


    protected function validator($value, $id = null)
    {
        $value = parent::validator($value);

        if (is_array($value)) {
            $value = serialize($value);
        }

        return $value;
    }

    public function getUnitData($value)
    {
        $unitData = parent::getUnitData($value);
        $value = $unitData['value'] ?: [];

        $countries = [];

        foreach (Gambling::getCountries() as $country) {
            if (empty($value[$country['id']])) {
                $value[$country['id']] = '';
            }
            $countries[$country['id']] = $country['code'];
        }

        $unitData['value'] = $value;
        $unitData['config']['countries'] = $countries;

        return $unitData;
    }


}
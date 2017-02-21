<?php

namespace Grow\Units;

use Grow\ActionData;

class Kit extends AbstractUnit
{

    public static $componentName = 'unit-kit';


    public function getUnitData($value)
    {
        $fieldsData = $this->fieldData['fields'];

        if (!$fieldsData) return null;

        $unitData =  parent::getUnitData($value);

        $fields = [];

        foreach ($fieldsData as $fieldName=>$fieldData) {
            if (empty($fieldData['inputType'])) continue;

            $unitClass = $this->getUnitClass($fieldData['inputType']);

            if (empty($unitClass)) continue;

            $unit = new $unitClass($this->table, $fieldName, $fieldData);

            $fields[$fieldName] = $unit->getUnitData($value ?: null);
        }

        $unitData['fields'] = $fields;

        return $unitData;
    }


    protected function getUnitClass($unitName)
    {
        $class = $GLOBALS['UNITS'][$unitName];
        return class_exists($class) ? $class : null;
    }

}
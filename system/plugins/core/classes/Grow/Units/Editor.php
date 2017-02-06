<?php

namespace Grow\Units;

class Editor extends AbstractUnit
{

    public static $componentName = 'unit-editor';

    public function getUnitData($value)
    {
        $unitData = parent::getUnitData($value);
        
        $preset = $this->fieldData['preset'] ?: 'regular';
        $unitData['config']['settings'] = $GLOBALS['EDITOR_PRESETS'][$preset] ?: [];

        if ($this->fieldData['config']['settings']) {
            $unitData['config']['settings'] = array_merge($unitData['config']['settings'], $this->fieldData['config']['settings']);
        }

        return $unitData;
    }

}
<?php

namespace Gambling;

use Grow\Organizer;

class OptionsOrganizer extends Organizer
{

    protected $optionsFile = TL_ROOT . '/system/config/options.php';


    public function __construct()
    {
        parent::__construct(null);
    }


    public function saveVariables($countryId, $variablesValues)
    {
        $allOptions = $this->loadAllOptions();

        foreach ($variablesValues as $variableName => $values) {
            if (empty($values)) continue;
            foreach ($values as $index=>$value) {
                if (!empty($value['id'])) continue;
                $variablesValues[$variableName][$index]['id'] = substr(md5($index . $value['label'] . time()), 0, 8);
            }
        }

        $allOptions[$countryId] = $variablesValues;

        $allOptions = var_export($allOptions, true);
        file_put_contents($this->optionsFile, "<?php return $allOptions ;");

        return null;
    }


    public function loadVariables($countryId, $variablesNames)
    {
        $options = $this->loadOptions($countryId);

        $fieldsData = [];

        foreach ($variablesNames as $variableName) {
            $fieldsData[$variableName] = [
                'label' => [$variableName, ''],
                'inputType' => 'kit',
                'fields' => [
                    'id' => [
                        'inputType' => 'text',
                        'hidden' => true
                    ],
                    'label' => [
                        'inputType' => 'text'
                    ]
                ],
                'config' => [
                    'head' => false
                ]
            ];
        }

        $unitsData = $this->getUnitsDataForFields($options, $variablesNames, $fieldsData);

        return $unitsData;
    }


    public function save($id, $fieldsValues)
    {
        throw new \Exception('Method is not useful');
    }


    public function load($id)
    {
        throw new \Exception('Method is not useful');
    }


    protected function loadAllOptions()
    {
        if (!file_exists($this->optionsFile)) return [];
        $allOptions = include $this->optionsFile;
        return $allOptions ?: [];
    }


    protected function loadOptions($countryId)
    {
        $allOptions = $this->loadAllOptions();
        if (!$allOptions || !$allOptions[$countryId] || !is_array($allOptions[$countryId])) {
            return [];
        }
        return $allOptions[$countryId];
    }

}
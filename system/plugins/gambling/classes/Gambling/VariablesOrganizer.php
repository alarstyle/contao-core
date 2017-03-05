<?php

namespace Gambling;

use Grow\Organizer;

class VariablesOrganizer extends Organizer
{

    protected $translationsFile = TL_ROOT . '/system/config/translations.php';


    public function __construct()
    {
        parent::__construct(null);
    }


    public function saveVariables($categoryName, $countryId, $variablesValues)
    {
        $allTranslations = $this->loadAllTranslations();

        $allTranslations[$categoryName][$countryId] = $variablesValues;

        $allTranslations = var_export($allTranslations, true);
        file_put_contents($this->translationsFile, "<?php return $allTranslations ;");

        return null;
    }


    public function loadVariables($categoryName, $countryId, $variablesNames)
    {
        $translations = $this->loadTranslations($categoryName, $countryId);

        $fieldsData = [];

        foreach ($variablesNames as $variableName) {
            $fieldsData[$variableName] = [
                'label' => [$variableName, ''],
                'inputType' => 'text'
            ];
        }

        $unitsData = $this->getUnitsDataForFields($translations, $variablesNames, $fieldsData);

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


    protected function loadAllTranslations()
    {
        if (!file_exists($this->translationsFile)) return [];
        $allTranslations = include $this->translationsFile;
        return $allTranslations ?: [];
    }


    protected function loadTranslations($categoryName, $countryId)
    {
        $allTranslations = $this->loadAllTranslations();
        if (!$allTranslations || !$allTranslations[$categoryName] || !$allTranslations[$categoryName][$countryId] || !is_array($allTranslations[$categoryName][$countryId])) {
            return [];
        }
        return $allTranslations[$categoryName][$countryId];
    }

}
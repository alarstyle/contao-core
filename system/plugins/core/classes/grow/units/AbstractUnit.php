<?php

namespace Grow\Units;

use Contao\BaseTemplate;
use Contao\Database;
use Contao\System;
use Grow\ActionData;

/**
 * Generates and validates form fields
 *
 * The class functions as abstract parent class for all editor classes and
 * provides methods to generate the form field markup and to validate the form
 * field input.
 */
abstract class AbstractUnit
{

    protected static $componentName;


    protected $table;


    protected $field;


    protected $fieldData;


    protected $attributes;


    protected $errorsArr = [];


    protected $skip = false;


    protected $database;


    public function __construct($table, $field)
    {
        $this->table = $table;
        $this->field = $field;
        $this->fieldData = $this->getFieldData();
        $this->attributes = $this->getAttributes();
        $this->database = Database::getInstance();
    }


    public function getUnitData($value)
    {
        $unitData = [
            'name' => $this->field,
            'component' => static::$componentName,
            'label' => is_array($this->fieldData['label']) ? $this->fieldData['label'][0] : $this->field,
            'hint' => is_array($this->fieldData['label']) ? $this->fieldData['label'][1] : '',
            'required' => $this->fieldData['eval']['mandatory'],
            'value' => array_key_exists('default', $this->fieldData) ? (is_array($this->fieldData['default']) ? serialize($this->fieldData['default']) : $this->fieldData['default']) : '',
            'config' => []
        ];

        $options = $this->getProcessedOptions();

        if ($options !== null) {
            $unitData['config']['options'] = $options;
        }

        if ($value !== null) {
            $unitData['value'] = deserialize($value);
        }

        if ($this->fieldData['eval']['tl_class']) {
            $unitData['config']['class'] = $this->fieldData['eval']['tl_class'];
        }

        return $unitData;
    }


    /**
     * Return true if has errors
     *
     * @return boolean True if there are errors
     */
    public function hasErrors()
    {
        return !empty($this->errorsArr);
    }


    /**
     * Return true if no need to submit the value
     *
     * @return boolean
     */
    public function skipSubmit()
    {
        return $this->skip;
    }


    /**
     * Return the errors array
     *
     * @return array An array of error messages
     */
    public function getErrors()
    {
        return $this->errorsArr;
    }


    /**
     * Validate the user input and set the value
     */
    public function validate($value, $id = null)
    {
        $processedValue = $this->validator($value, $id);
        return $processedValue;
    }


    protected function validator($value, $id = null)
    {
        $attr = $this->fieldData;

        if ($attr['required'] && empty($value)) {
            $this->errorsArr[] = "Can't be empty";
        }

        // Make sure unique fields are unique
        if ($attr['eval']['unique'] && $value != '' && !$this->database->isUniqueValue($this->table, $this->field, $value, $id))
        {
            $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['unique'], $attr['label'][0] ?: $this->field);
        }

        return $value;
    }


    protected function getFieldData()
    {
        return $GLOBALS['TL_DCA'][$this->table]['fields'][$this->field];
    }


    protected function getAttributes()
    {
        $attr = $GLOBALS['TL_DCA'][$this->table]['fields'][$this->field];

        if (!empty($attr['eval'])) {
            $attr['config'] = $attr['eval'];
        }

        return $attr;
    }


    protected function getProcessedOptions()
    {
        $fieldData = $this->fieldData;

        if (is_array($fieldData['options_callback'])) {
            $arrCallback = $fieldData['options_callback'];
            $fieldData['options'] = System::importStatic($arrCallback[0])->{$arrCallback[1]}();
        }
        elseif (is_callable($fieldData['options_callback'])) {
            $fieldData['options'] = call_user_func($fieldData['options_callback']);
        }
        elseif (isset($fieldData['foreignKey'])) {
            $arrKey = explode('.', $fieldData['foreignKey'], 2);
            $objOptions = Database::getInstance()->query("SELECT id, " . $arrKey[1] . " AS value FROM " . $arrKey[0] . " WHERE tstamp>0 ORDER BY value");
            $fieldData['options'] = [];
            while ($objOptions->next()) {
                $fieldData['options'][$objOptions->id] = $objOptions->value;
            }
        }

        $options = $fieldData['options'];

        if (!is_array($options) || empty($options)) {
            return null;
        }

        $validOptions = [];

        if ($fieldData['eval']['includeBlankOption'] && !$fieldData['eval']['multiple']) {
            $validOptions[] = [
                'value' => '',
                'label' => '-'
            ];
        }

//        echo '<br><br>';
//        var_dump($fieldData['reference']);
//        var_dump($options);
//        echo '<br><br>';

        /*
        foreach ($options as $key => $value) {
            $validOptions[] = [
                'value' => $isAssociative ? $key : $value,
                'label' => $key//!$isReference ? $value : ((($ref = (is_array($fieldData['reference'][$value]) ? $fieldData['reference'][$value][0] : $fieldData['reference'][$value])) != false) ? $ref : $value)
            ];
        }
        */

        $validOptions = array_merge($validOptions, $this->normalizeOptions($options));

        return $validOptions;
    }


    protected function normalizeOptions($options)
    {
        $normalOptions = [];

        $fieldData = $this->fieldData;

        $isAssociative = array_is_assoc($options);
        $isReference = isset($fieldData['reference']);

        foreach ($options as $key => $value) {
            if ($isReference) {
                if (is_array($value)) {
                    $label = $key;
                }
                else {
                    $ref = is_array($fieldData['reference'][$value]) ? $fieldData['reference'][$value][0] : $fieldData['reference'][$value];
                    $label = $ref ?: $value;
                }
            }
            else {
                $label = $value;
            }
            $normalOptions[] = [
                'value' => $isAssociative ? $key : $value,
                'label' => $label
            ];
        }

        return $normalOptions;
    }


    protected function getProcessedValue()
    {

    }

}
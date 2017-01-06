<?php

namespace Grow\Units;

use Contao\BaseTemplate;
use Contao\Database;
use Contao\Date;
use Contao\Idna;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
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

        if (empty($this->fieldData['sql'])) {
            $this->skip = true;
        }
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

        if ($this->fieldData['config']) {
            foreach ($this->fieldData['config'] as $key=>$value) {
                $unitData['config'][$key] = $value;
            }
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

        if (is_string($value)) {
            $value = trim($value);
        }

        if ($attr['required'] && empty($value)) {
            $this->errorsArr[] = "Can't be empty";
        }

        //
        if (!empty($attr['eval']['rgxp'])) {
            $this->validateRgxp($value, $id, $attr['eval']['rgxp']);
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

        if (!is_array($options)) {
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
                'value' => ($isAssociative ? $key : $value) . '',
                'label' => $label
            ];
        }

        return $normalOptions;
    }


    protected function getProcessedValue()
    {

    }


    protected function validateRgxp($value, $id, $rgxp)
    {
        $label = $this->fieldData['label'];

        switch ($rgxp)
        {
            // Numeric characters (including full stop [.] and minus [-])
            case 'digit':
                // Support decimal commas and convert them automatically (see #3488)
                if (substr_count($value, ',') == 1 && strpos($value, '.') === false)
                {
                    $value = str_replace(',', '.', $value);
                }
                if (!Validator::isNumeric($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['digit'], $label);
                }
                break;

            // Natural numbers (positive integers)
            case 'natural':
                if (!Validator::isNatural($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['natural'], $label);
                }
                break;

            // Alphabetic characters (including full stop [.] minus [-] and space [ ])
            case 'alpha':
                if (!Validator::isAlphabetic($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['alpha'], $label);
                }
                break;

            // Alphanumeric characters (including full stop [.] minus [-], underscore [_] and space [ ])
            case 'alnum':
                if (!Validator::isAlphanumeric($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['alnum'], $label);
                }
                break;

            // Do not allow any characters that are usually encoded by class Input [=<>()#/])
            case 'extnd':
                if (!Validator::isExtendedAlphanumeric(html_entity_decode($value)))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['extnd'], $label);
                }
                break;

            // Check whether the current value is a valid date format
            case 'date':
                if (!Validator::isDate($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['date'], Date::getInputFormat(Date::getNumericDateFormat()));
                }
                else
                {
                    // Validate the date (see #5086)
                    try
                    {
                        new Date($value, Date::getNumericDateFormat());
                    }
                    catch (\OutOfBoundsException $e)
                    {
                        $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['invalidDate'], $value);
                    }
                }
                break;

            // Check whether the current value is a valid time format
            case 'time':
                if (!Validator::isTime($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['time'], Date::getInputFormat(Date::getNumericTimeFormat()));
                }
                break;

            // Check whether the current value is a valid date and time format
            case 'datim':
                if (!Validator::isDatim($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['dateTime'], Date::getInputFormat(Date::getNumericDatimFormat()));
                }
                else
                {
                    // Validate the date (see #5086)
                    try
                    {
                        new Date($value, Date::getNumericDatimFormat());
                    }
                    catch (\OutOfBoundsException $e)
                    {
                        $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['invalidDate'], $value);
                    }
                }
                break;

            // Check whether the current value is a valid e-mail address
            case 'email':
                if (!Validator::isEmail($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['email'], $label);
                }
                break;

            // Check whether the current value is list of valid e-mail addresses
            case 'emails':
                $arrEmails = trimsplit(',', $value);

                foreach ($arrEmails as $strEmail)
                {
                    $strEmail = Idna::encodeEmail($strEmail);

                    if (!Validator::isEmail($strEmail))
                    {
                        $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['emails'], $label);
                        break;
                    }
                }
                break;

            // Check whether the current value is a valid URL
            case 'url':
                if (!Validator::isUrl($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['url'], $label);
                }
                break;

            // Check whether the current value is a valid alias
            case 'alias':
                if (!Validator::isAlias($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['alias'], $label);
                }
                break;

            // Check whether the current value is a valid folder URL alias
            case 'folderalias':
                if (!Validator::isFolderAlias($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['folderalias'], $label);
                }
                break;

            // Check whether the current value is a valid page URL alias
            case 'pagealias':
                if (!Validator::isPageAlias($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['folderalias'], $label);
                }
                break;

            // Phone numbers (numeric characters, space [ ], plus [+], minus [-], parentheses [()] and slash [/])
            case 'phone':
                if (!Validator::isPhone(html_entity_decode($value)))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['phone'], $label);
                }
                break;

            // Check whether the current value is a percent value
            case 'prcnt':
                if (!Validator::isPercent($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['prcnt'], $label);
                }
                break;

            // Check whether the current value is a locale
            case 'locale':
                if (!Validator::isLocale($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['locale'], $label);
                }
                break;

            // Check whether the current value is a language code
            case 'language':
                if (!Validator::isLanguage($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['language'], $label);
                }
                break;

            // Check whether the current value is a Google+ ID or vanity name
            case 'google+':
                if (!Validator::isGooglePlusId($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['invalidGoogleId'], $label);
                }
                break;

            // Check whether the current value is a field name
            case 'fieldname':
                if (!Validator::isFieldName($value))
                {
                    $this->errorsArr[] = sprintf($GLOBALS['TL_LANG']['ERR']['invalidFieldName'], $label);
                }
                break;

            // HOOK: pass unknown tags to callback functions
//            default:
//                if (isset($GLOBALS['TL_HOOKS']['addCustomRegexp']) && is_array($GLOBALS['TL_HOOKS']['addCustomRegexp']))
//                {
//                    foreach ($GLOBALS['TL_HOOKS']['addCustomRegexp'] as $callback)
//                    {
//                        $this->import($callback[0]);
//                        $break = $this->{$callback[0]}->{$callback[1]}($rgxp, $value, $this);
//
//                        // Stop the loop if a callback returned true
//                        if ($break === true)
//                        {
//                            break;
//                        }
//                    }
//                }
//                break;
        }
    }

}
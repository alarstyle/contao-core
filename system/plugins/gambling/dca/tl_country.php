<?php

/**
 * Table tl_country
 */
$GLOBALS['TL_DCA']['tl_country'] = array
(

	// Config
	'config' => [
		'dataContainer'               => 'Table',
		'sql' => [
			'keys' => [
				'id' => 'primary',
                'country' => 'unique',
            ]
		]
	],

	// List
	'list' => [
		'label' => [
			'fields'                  => ['country'],
			'callback'                => ['tl_country', 'listCallback']
		]
    ],

	// Palettes
	'palettes' => [
		'default'                     => 'country, language, fallback'
	],

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
        'country' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_country']['country'],
            'inputType'               => 'select',
            'options_callback'        => ['tl_country', 'getCountriesAsOptions'],
            'required'                => true,
            'eval'                    => array('mandatory'=>true, 'unique'=>true),
            'sql'                     => "varchar(5) NOT NULL"
        ),
        'language' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_country']['language'],
            'inputType'               => 'select',
            'required'                => true,
			'options'                 => \Contao\System::getLanguages(),
            'eval'                    => array('mandatory'=>true),
			'sql'                     => "varchar(5) NOT NULL default ''"
        ),
        'fallback' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_country']['fallback'],
            'inputType'               => 'checkbox',
            'save_callback_new' => [
                ['tl_country', 'fallbackReset']
            ],
            'sql'                     => "char(1) NOT NULL default ''"
        ),
	)
);


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
use Contao\Backend;

class tl_country extends \Contao\Backend
{


	public function listCallback($item)
	{
		$item['fields'][0] = \Contao\System::getCountriesWithFlags()[$item['fields'][0]];
		return $item;
	}


	public function getCountriesAsOptions()
    {
        $countries = \Contao\System::getCountriesWithFlags();
        $arr = [];

        foreach ($countries as $key => $name) {
            $arr[] = [
                'value' => $key,
                'label' => $name
            ];
        }

        return $countries;
    }


    public function fallbackReset($value, $id, &$fieldsValues)
    {
        if ($value == 1) {

            $database = \Contao\Database::getInstance();

            if ($id) {
                $statement = $database->prepare("UPDATE tl_country SET fallback = 0 WHERE id <> ?")
                    ->execute(intval($id));
            } else {
                $database->prepare("UPDATE tl_country SET fallback = 0")
                    ->execute();
            }

        }

        return $value;
    }

}

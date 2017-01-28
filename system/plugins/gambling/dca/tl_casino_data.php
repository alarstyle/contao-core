<?php

$GLOBALS['TL_DCA']['tl_casino_data'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'sql' => [
			'keys' => [
				'id' => 'primary'
            ]
        ]
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'fields'                  => array('dateAdded DESC')
		),
		'label' => array
		(
            'fields_new'              => array('img_logo', 'name', 'countries'),
		)
    ),

	'palettes' => [
		'default'                     => '[bonuses],cash_sign_up,spins_sign_up,cash_1_deposit,spins_1_deposit,cash_2_deposit,spins_2_deposit,[seo],meta_title,meta_description,[pros_cons],pros,cons'
	],

	'fields' => [
		'id' => [
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		],
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
		'pid' => [
			'sql'                     => "int(10) unsigned NOT NULL"
		],
        'country' => [
            'sql'                     => "int(10) unsigned NOT NULL"
        ],
        'cash_sign_up' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cash_sign_up'],
            'inputType'               => 'text',
            'required'                => true,
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'spins_sign_up' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['spins_sign_up'],
            'inputType'               => 'text',
            'required'                => true,
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'cash_1_deposit' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cash_1_deposit'],
            'inputType'               => 'text',
            'required'                => true,
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'spins_1_deposit' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['spins_1_deposit'],
            'inputType'               => 'text',
            'required'                => true,
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'cash_2_deposit' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cash_2_deposit'],
            'inputType'               => 'text',
            'required'                => true,
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'spins_2_deposit' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['spins_2_deposit'],
            'inputType'               => 'text',
            'required'                => true,
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'meta_title' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['meta_title'],
            'inputType'         => 'text',
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'meta_description' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['meta_description'],
            'inputType'         => 'textarea',
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'pros' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['pros'],
            'inputTypeNew'            => 'textarea',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "text NULL"
        ],
        'cons' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cons'],
            'inputTypeNew'            => 'textarea',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "text NULL"
        ]
    ]
);


use Contao\Backend;

class tl_casino_data extends \Contao\Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Contao\\BackendUser', 'User');
	}


	public function loadType($value, $id, &$fieldsValues)
    {
        if ($fieldsValues['isCasino'] == 1 && $fieldsValues['isBetting'] == 1) {
            return 'casino_betting';
        }
        if ($fieldsValues['isCasino'] == 1) {
            return 'casino';
        }
        if ($fieldsValues['isBetting'] == 1) {
            return 'betting';
        }
        return null;
    }


	public function saveType($value, $id, &$fieldsValues)
	{
        switch ($value) {
            case 'casino':
                $fieldsValues['isCasino'] = 1;
                $fieldsValues['isBetting'] = 0;
                break;
            case 'betting':
                $fieldsValues['isCasino'] = 0;
                $fieldsValues['isBetting'] = 1;
                break;
            case 'casino_betting':
                $fieldsValues['isCasino'] = 1;
                $fieldsValues['isBetting'] = 1;
                break;
            default:
                $fieldsValues['isCasino'] = 0;
                $fieldsValues['isBetting'] = 0;
        }
		return null;
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
}

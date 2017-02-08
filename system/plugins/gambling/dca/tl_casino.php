<?php

$GLOBALS['TL_DCA']['tl_casino'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
                'alias' => 'unique',
			)
		)
	),

	// List
	'list' => array
	(
		'label' => array
		(
            'fields_new'              => array('img_logo', 'name', 'countries'),
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_casino']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
				'icon_new'            => 'pencil',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_casino']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'icon_new'            => 'trash',
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '[general],countries,name,alias,website,owner,year,licence,phone,email,rating,type,casino_categories,betting_categories,[images],img_logo,img_cover,logo_bg'
	),

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
        'countries' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['countries'],
            'inputTypeNew'            => 'checkboxWizard',
            'options_callback'        => 'Gambling\\BackendHelpers::getCountriesForOptions',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "blob NULL"
        ),
		'name' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['name'],
			'inputType'               => 'text',
            'required'                => true,
			'eval'                    => array('mandatory'=>true, 'tl_class'=>'unit--long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		],
        'alias' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['alias'],
            'inputType'               => 'text',
            'required'                => true,
            'config'                  => [
                'prefix' => \Contao\Environment::get('base') . '../casino/'
            ],
            'eval'                    => array('mandatory'=>true, 'unique'=>true, 'rgxp'=>'alias', 'tl_class'=>'unit--long'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
		'website' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['website'],
			'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'unit--long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
        ],
		'owner' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['owner'],
			'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'unit--long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		],
		'year' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['year'],
			'inputType'               => 'text',
			'sql'                     => "varchar(255) NOT NULL default ''"
		],
		'licence' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['licence'],
			'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'unit--long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		],
		'rating' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['rating'],
			'inputType'               => 'rating',
			'sql'                     => "float NULL"
		],
        'type' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino']['type'],
            'inputType'         => 'radio',
            'default'           => 'casino',
            'options'           => ['casino' => 'Only Casino', 'betting' => 'Only Betting', 'casino_betting' => 'Casino And Betting'],
            'load_callback_new' => [
                ['tl_casino', 'loadType']
            ],
            'save_callback_new' => [
                ['tl_casino', 'saveType']
            ]
        ],
        'is_casino' => [
            'sql'               => "char(1) NOT NULL default ''"
        ],
        'is_betting' => [
            'sql'               => "char(1) NOT NULL default ''"
        ],
        'casino_categories' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['casino_categories'],
            'inputTypeNew'            => 'checkboxWizard',
            'options_callback'        => 'Gambling\\BackendHelpers::getCasinoCategoriesForOptions',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "blob NULL"
        ],
        'betting_categories' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['betting_categories'],
            'inputTypeNew'            => 'checkboxWizard',
            'options_callback'        => 'Gambling\\BackendHelpers::getBettingCategoriesForOptions',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "blob NULL"
        ],
        'img_logo' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino']['img_logo'],
            'inputType'         => 'filePicker',
            'eval'              => ['tl_class'=>'w50'],
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'logo_bg' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino']['logo_bg'],
            'inputType'         => 'color',
            'eval'              => ['tl_class'=>'w50'],
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'img_cover' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino']['img_cover'],
            'inputType'         => 'filePicker',
            'eval'              => ['tl_class'=>'w50'],
            'sql'               => "varchar(255) NOT NULL default ''"
        ]
	)
);


use Contao\Backend;

class tl_casino extends \Contao\Backend
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
        if ($fieldsValues['is_casino'] == 1 && $fieldsValues['is_betting'] == 1) {
            return 'casino_betting';
        }
        if ($fieldsValues['is_casino'] == 1) {
            return 'casino';
        }
        if ($fieldsValues['is_betting'] == 1) {
            return 'betting';
        }
        return null;
    }


	public function saveType($value, $id, &$fieldsValues)
	{
        switch ($value) {
            case 'casino':
                $fieldsValues['is_casino'] = 1;
                $fieldsValues['is_betting'] = 0;
                break;
            case 'betting':
                $fieldsValues['is_casino'] = 0;
                $fieldsValues['is_betting'] = 1;
                break;
            case 'casino_betting':
                $fieldsValues['is_casino'] = 1;
                $fieldsValues['is_betting'] = 1;
                break;
            default:
                $fieldsValues['is_casino'] = 0;
                $fieldsValues['is_betting'] = 0;
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

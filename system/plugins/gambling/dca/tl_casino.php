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
		'default'                     => '[general],countries,name,alias,url,owner,year,licence,phone,email,rating,type,casino_link,betting_link,casino_categories,betting_categories,[images],img_logo,img_cover'
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
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['name'],
			'inputType'               => 'text',
            'required'                => true,
			'eval'                    => array('mandatory'=>true, 'tl_class'=>'unit--long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'alias' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['alias'],
            'inputType'               => 'text',
            'required'                => true,
            'config'                  => [
                'prefix' => \Contao\Environment::get('base') . '../casino/'
            ],
            'eval'                    => array('mandatory'=>true, 'unique'=>true, 'rgxp'=>'alias', 'tl_class'=>'unit--long'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
		'url' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['url'],
			'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'unit--long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'owner' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['owner'],
			'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'unit--long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'year' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['year'],
			'inputType'               => 'text',
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'licence' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['licence'],
			'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'unit--long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'phone' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['phone'],
			'inputType'               => 'text',
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'email' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['email'],
			'inputType'               => 'text',
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'rating' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['rating'],
			'inputType'               => 'rating',
			'sql'                     => "float NULL"
		),
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
        'isCasino' => [
            'sql'               => "char(1) NOT NULL default ''"
        ],
        'isBetting' => [
            'sql'               => "char(1) NOT NULL default ''"
        ],
        'casino_link' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['casino_link'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'betting_link' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['betting_link'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'casino_categories' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['casino_categories'],
            'inputTypeNew'            => 'checkboxWizard',
            'options_callback'        => 'Gambling\\BackendHelpers::getCasinoCategoriesForOptions',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "blob NULL"
        ),
        'betting_categories' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['betting_categories'],
            'inputTypeNew'            => 'checkboxWizard',
            'options_callback'        => 'Gambling\\BackendHelpers::getBettingCategoriesForOptions',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "blob NULL"
        ),
        'img_logo' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino']['img_logo'],
            'inputType'         => 'filePicker',
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

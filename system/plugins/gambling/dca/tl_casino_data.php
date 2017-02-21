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
		'default'                     => '[categories_title],casino_categories,betting_categories,[links],casino_link,casino_same_window,betting_link,betting_same_window,[misc],currency,currency_before,bonus_code,[casino_bonuses],cash_sign_up,spins_sign_up,cash_1_deposit,spins_1_deposit,cash_2_deposit,spins_2_deposit,cash_3_deposit,spins_3_deposit,[betting_bonuses],bet_bonus_sign_up,bet_bonus_deposit,[wagering_title],wagering,[withdrawal],withdrawal_methods,withdrawal_min,withdrawal_max,withdrawal_frequency,[deposit_methods_title],deposit_methods,[providers_title],providers,[licenses_title],licenses,[contacts],phone,email,[review_title],review,[pros_cons],pros,cons,[seo],meta_title,meta_description',
        'sidebar'                     => 'published'
	],

	'fields' => [
		'id' => [
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		],
        'tstamp' => [
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
		'pid' => [
			'sql'                     => "int(10) unsigned NOT NULL"
		],
        'country' => [
            'sql'                     => "int(10) unsigned NOT NULL"
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
        'casino_link' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['casino_link'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'casino_same_window' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['casino_same_window'],
            'inputType'               => 'checkbox',
            'eval'                    => array('tl_class'=>'w50 align_to_text'),
            'sql'                     => "char(1) NOT NULL default ''"
        ],
        'betting_link' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['betting_link'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w50 clr'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'betting_same_window' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['betting_same_window'],
            'inputType'               => 'checkbox',
            'eval'                    => array('tl_class'=>'w50 align_to_text'),
            'sql'                     => "char(1) NOT NULL default ''"
        ],
        'currency' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['currency'],
            'inputType'               => 'text',
            'required'                => true,
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'currency_before' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['currency_before'],
            'inputType'               => 'checkbox',
            'eval'                    => array('tl_class'=>'w50 align_to_text'),
            'sql'                     => "char(1) NOT NULL default ''"
        ),
        'bonus_code' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['bonus_code'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50 clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'cash_sign_up' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cash_sign_up'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'spins_sign_up' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['spins_sign_up'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'cash_1_deposit' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cash_1_deposit'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'spins_1_deposit' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['spins_1_deposit'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'cash_2_deposit' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cash_2_deposit'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'spins_2_deposit' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['spins_2_deposit'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'cash_3_deposit' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cash_3_deposit'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'spins_3_deposit' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['spins_3_deposit'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'bet_bonus_sign_up' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['bet_bonus_sign_up'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'bet_bonus_deposit' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['bet_bonus_deposit'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'wagering' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['wagering'],
            'inputType'         => 'textarea',
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'withdrawal_methods' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['withdrawal_methods'],
            'inputType'         => 'textarea',
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'withdrawal_min' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['withdrawal_min'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'withdrawal_max' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['withdrawal_max'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'withdrawal_frequency' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['withdrawal_frequency'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'long'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'deposit_methods' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['deposit_methods'],
            'inputType'         => 'textarea',
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'providers' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['providers'],
            'inputType'         => 'textarea',
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'licenses' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['licenses'],
            'inputType'               => 'editor',
            'sql'                     => "mediumtext NULL",
            'config'                  => [
                'settings' => [
                    'toolbar' => 'removeformat | link unlink | code'
                ]
            ]
        ],
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
        'review' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['review'],
            'inputType'               => 'editor',
            'sql'                     => "mediumtext NULL",
            'config'                  => []
        ),
        'cons' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cons'],
            'inputTypeNew'            => 'textarea',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "text NULL"
        ],
        'phone' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['phone'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'email' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['email'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'published' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['published'],
            'inputType'               => 'checkbox',
            'sql'                     => "char(1) NOT NULL default ''"
        ),
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

}

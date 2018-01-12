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
		'default'                     => '[categories_title],casino_categories,betting_categories,[links],casino_link,casino_same_window,betting_link,betting_same_window,terms_link,[misc],casino_rating,betting_rating,currency,currency_before,casino_code,betting_code,[casino_signup_bonuses],cash_sign_up,spins_sign_up,deposit_bonuses,[betting_bonuses],bet_bonus_sign_up,bet_bonus_deposit_percent,bet_bonus_deposit,[wagering_title],wagering_casino_spins,wagering_casino_deposit,wagering_casino_type,wagering_betting_odds,wagering_betting_bonus,wagering_betting_type,[withdrawal],withdrawal_methods,withdrawal_min,withdrawal_max,withdrawal_frequency,[deposit_title],deposit_min,deposit_max,deposit_methods,[providers_title],providers,[licenses_title],licenses,[contacts],phone,phone_hours,livechat,livechat_hours,email,[review_title],review,[pros_cons],pros,cons,[seo],meta_title,meta_description',
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
        'terms_link' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['terms_link'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w50 clr'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'casino_rating' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['casino_rating'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "float NULL"
        ],
        'betting_rating' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['betting_rating'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "float NULL"
        ],
        'currency' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['currency'],
            'inputType'               => 'text',
            'required'                => true,
            'eval'                    => array('tl_class'=>'w50 clr'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'currency_before' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['currency_before'],
            'inputType'               => 'checkbox',
            'eval'                    => array('tl_class'=>'w50 align_to_text'),
            'sql'                     => "char(1) NOT NULL default ''"
        ),
        'casino_code' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['casino_code'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'betting_code' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['betting_code'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50'),
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
//        'cash_1_deposit' => [
//            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cash_1_deposit'],
//            'inputType'               => 'text',
//            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
//            'sql'                     => "varchar(255) NOT NULL default ''"
//        ],
//        'spins_1_deposit' => [
//            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['spins_1_deposit'],
//            'inputType'               => 'text',
//            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
//            'sql'                     => "varchar(255) NOT NULL default ''"
//        ],
//        'cash_2_deposit' => [
//            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cash_2_deposit'],
//            'inputType'               => 'text',
//            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
//            'sql'                     => "varchar(255) NOT NULL default ''"
//        ],
//        'spins_2_deposit' => [
//            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['spins_2_deposit'],
//            'inputType'               => 'text',
//            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
//            'sql'                     => "varchar(255) NOT NULL default ''"
//        ],
//        'cash_3_deposit' => [
//            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['cash_3_deposit'],
//            'inputType'               => 'text',
//            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
//            'sql'                     => "varchar(255) NOT NULL default ''"
//        ],
//        'spins_3_deposit' => [
//            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['spins_3_deposit'],
//            'inputType'               => 'text',
//            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
//            'sql'                     => "varchar(255) NOT NULL default ''"
//        ],
        'deposit_bonuses' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['deposit_bonuses'],
            'inputType'         => 'kit',
            'fields' => [
                'percent' => [
                    'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['deposit_bonuses_percent'],
                    'inputType'               => 'text',
                    'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit')
                ],
                'cash' => [
                    'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['deposit_bonuses_cash'],
                    'inputType'               => 'text',
                    'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit')
                ],
                'spins' => [
                    'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['deposit_bonuses_spins'],
                    'inputType'               => 'text',
                    'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit')
                ]
            ],
            'config' => [
                'enumerable' => true,
                'max' => 7
            ],
            'sql'                     => "blob NULL"
        ],
        'bet_bonus_sign_up' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['bet_bonus_sign_up'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'bet_bonus_deposit_percent' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['bet_bonus_deposit_percent'],
            'inputType'               => 'text',
            'config'                  => [
                'prefix' => '%'
            ],
            'eval'                    => array('tl_class'=>'w25', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'bet_bonus_deposit' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['bet_bonus_deposit'],
            'inputType'               => 'text',
            'config'                  => [
                'prefix' => 'up to'
            ],
            'eval'                    => array('tl_class'=>'w25', 'rgxp'=>'digit'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'wagering_casino_spins' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['wagering_casino_spins'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w25'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'wagering_casino_deposit' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['wagering_casino_deposit'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w25'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'wagering_casino_type' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['wagering_casino_type'],
            'inputType'         => 'radio',
            'default'           => 'bonus',
            'eval'              => ['tl_class'=>'w50'],
            'options'           => ['bonus' => 'bonus', 'deposit + bonus' => 'deposit + bonus'],
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'wagering_betting_odds' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['wagering_betting_odds'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w25 clr'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'wagering_betting_bonus' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['wagering_betting_bonus'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w25'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'wagering_betting_type' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['wagering_betting_type'],
            'inputType'         => 'radio',
            'default'           => 'bonus',
            'eval'              => ['tl_class'=>'w50'],
            'options'           => ['bonus' => 'bonus', 'deposit + bonus' => 'deposit + bonus'],
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
//        'wagering_casino' => [
//            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['wagering_casino'],
//            'inputType'         => 'checkboxWizard',
//            'eval'              => array('tl_class'=>'w50'),
//            'sql'               => "varchar(500) NOT NULL default ''"
//        ],
//        'wagering_betting' => [
//            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['wagering_betting'],
//            'inputType'         => 'checkboxWizard',
//            'eval'              => array('tl_class'=>'w50'),
//            'sql'               => "varchar(500) NOT NULL default ''"
//        ],
        'withdrawal_methods' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['withdrawal_methods'],
            'inputType'         => 'checkboxWizard',
            'sql'               => "blob NULL"
        ],
        'withdrawal_min' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['withdrawal_min'],
            'inputType'         => 'text',
            'eval'              => array('tl_class'=>'w50', 'rgxp'=>'digit'),
            'sql'               => "varchar(255) NOT NULL default ''"
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
        'deposit_min' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['deposit_min'],
            'inputType'         => 'text',
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'deposit_max' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['deposit_max'],
            'inputType'         => 'text',
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'deposit_methods' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['deposit_methods'],
            'inputType'         => 'checkboxWizard',
            'sql'               => "blob NULL"
        ],
        'providers' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['providers'],
            'inputType'         => 'checkboxWizard',
            'sql'               => "blob NULL"
        ],
        'licenses' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['licenses'],
            'inputType'         => 'checkboxWizard',
            'sql'               => "blob NULL"
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
        'phone_hours' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['phone_hours'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'livechat' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_casino_data']['livechat'],
            'inputType'         => 'radio',
            'default'           => '0',
            'eval'              => ['tl_class'=>'w50 clr'],
            'options'           => ['1' => 'Yes', '0' => 'No'],
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'livechat_hours' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['livechat_hours'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'email' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_data']['email'],
            'inputType'               => 'text',
            'eval'                    => ['tl_class'=>'w50 clr'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'casino_sorting' => [
            'sql'               => "int(10) unsigned NOT NULL default '0'"
        ],
        'betting_sorting' => [
            'sql'               => "int(10) unsigned NOT NULL default '0'"
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

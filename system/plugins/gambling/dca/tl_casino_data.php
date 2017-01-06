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
		'default'                     => '[general],countries,name,url,owner,year,licence,phone,email,rating,type,casino_link,betting_link,casino_categories,betting_categories,[pros_cons],pros,cons'
	],

	'fields' => [
		'id' => [
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		],
		'pid' => [
			'sql'                     => "int(10) unsigned NOT NULL"
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

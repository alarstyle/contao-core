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
                'country' => 'unique',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'fields'                  => array('dateAdded DESC'),
			'flag'                    => 1,
		),
		'label' => array
		(
			'fields'                  => array('country'),
			'callback'                => array('tl_casino', 'listCallback')
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
		'default'                     => 'countries,name,url,owner,year,licence,phone,email'
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
            'label'                   => ['Casino Countries', ''],
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
			'eval'                    => array('mandatory'=>true),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'url' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['url'],
			'inputType'               => 'text',
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'owner' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['owner'],
			'inputType'               => 'text',
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
		)
	)
);


use Contao\Backend;
use Contao\Image;
use Contao\Input;

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


	public function listCallback($item)
	{
		$item['fields'][0] = \Contao\System::getCountriesWithFlags()[$item['fields'][0]];
		return $item;
	}


	/**
	 * Return all modules except profile modules
	 *
	 * @return array
	 */
	public function getModules()
	{
		$arrModules = array();

		foreach ($GLOBALS['BE_MOD'] as $k=>$v)
		{
			if (!empty($v))
			{
				unset($v['undo']);
				$arrModules[$k] = array_keys($v);
			}
		}

		return $arrModules;
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

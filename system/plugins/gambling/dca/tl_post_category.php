<?php

$GLOBALS['TL_DCA']['tl_post_category'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
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
			'callback'                => array('tl_post_category', 'listCallback')
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => 'name,alias'
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
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_post_category']['name'],
			'inputType'               => 'text',
            'required'                => true,
			'eval'                    => array('mandatory'=>true),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_post_category']['alias'],
			'inputType'               => 'text',
            'required'                => true,
			'eval'                    => array('mandatory'=>true, 'unique'=>true),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'country' => [
            'sql'                     => "int(10) NULL"
        ]
	)
);


use Contao\Backend;

class tl_post_category extends \Contao\Backend
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

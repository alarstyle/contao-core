<?php

$GLOBALS['TL_DCA']['tl_post'] = array
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
		'label' => array
		(
            'fields_new'              => array('img_preview', 'name', 'username', 'countries'),
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_post']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
				'icon_new'            => 'pencil',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_post']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'icon_new'            => 'trash',
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => 'name,alias,teaser,text,img_preview,img_cover,description',
        'sidebar'                     => 'category,published'
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_post']['name'],
			'inputType'               => 'text',
            'required'                => true,
			'eval'                    => array('mandatory'=>true, 'tl_class'=>'unit--long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_post']['alias'],
			'inputType'               => 'text',
            'required'                => true,
			'eval'                    => array('mandatory'=>true),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'teaser' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_post']['teaser'],
			'inputType'               => 'textarea',
            'sql'                     => "mediumtext NULL"
		),
		'text' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_post']['text'],
			'inputType'               => 'textarea',
            'sql'                     => "mediumtext NULL"
		),
        'img_preview' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_post']['img_preview'],
            'inputType'         => 'filePicker',
            'eval'              => ['tl_class'=>'w50'],
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'img_cover' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_post']['img_cover'],
            'inputType'         => 'filePicker',
            'eval'              => ['tl_class'=>'w50'],
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'description' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_post']['description'],
            'inputType'         => 'textarea',
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'category' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_post']['category'],
            'inputType'               => 'select',
            'required'                => true,
            //'options_callback'                 => ['tl_post', 'getCategoriesList'],
            'foreignKey'              => 'tl_post_category.name',
            'eval'                    => ['mandatory' => true],
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
        'date' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_post']['date'],
            'default'                 => time(),
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'date', 'datepicker'=>true),
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
        'published' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_post']['published'],
            'inputType'               => 'checkbox',
            'sql'                     => "char(1) NOT NULL default ''"
        ),
	)
);


use Contao\Backend;
use Contao\Image;
use Contao\Input;

class tl_post extends \Contao\Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Contao\\BackendUser', 'User');
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


	public function getCategoriesList()
    {
        $db = \Contao\Database::getInstance();

        $objRow = $db->prepare('SELECT * FROM tl_post_category')
            ->execute();

        if ($objRow->numRows < 1) {
            return [];
        }

        $categories = [];

        while ($objRow->next()) {
            $categories[$objRow->id] = $objRow->name;
        }

        return [];

        return $categories;
    }
}

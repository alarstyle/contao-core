<?php

/**
 * Table tl_casino_category
 */
$GLOBALS['TL_DCA']['tl_casino_category'] = array
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
				'id' => 'primary'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => 'name,alias,metaTitle,metaDescription,topTitle,topText,bottomTitle,bottomText'
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['name'],
            'inputType'               => 'text',
            'required'                => true,
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "blob NULL"
        ),
        'alias' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['alias'],
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "blob NULL"
        ],
        'metaTitle' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['metaTitle'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'unit--long'),
            'sql'                     => "blob NULL"
        ),
        'metaDescription' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['metaDescription'],
            'inputType'               => 'textarea',
            'sql'                     => "blob NULL"
        ),
        'topTitle' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['topTitle'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'unit--long'),
            'sql'                     => "blob NULL"
        ),
        'topText' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['topText'],
            'inputType'               => 'editor',
            'config' => ['settings' => $GLOBALS['EDITOR_PRESETS']['simple']],
            'sql'                     => "blob NULL"
        ),
        'bottomTitle' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['bottomTitle'],
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'unit--long'),
            'sql'                     => "blob NULL"
        ),
        'bottomText' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['bottomText'],
            'inputType'               => 'editor',
            //'config' => ['settings' => $GLOBALS['EDITOR_PRESETS']['simple']],
            'sql'                     => "blob NULL"
        ),
        'is_betting' => [
            'sql'               => "char(1) NOT NULL default ''"
        ]
	)
);
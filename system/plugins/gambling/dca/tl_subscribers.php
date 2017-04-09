<?php

$GLOBALS['TL_DCA']['tl_subscribers'] = array
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
		'default'                     => '[general],countries,name,alias,website,owner,year,licence,phone,email,type,languages,[images],img_logo,img_cover,logo_bg'
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
        'country' => [
            'sql'                     => "int(10) unsigned NOT NULL"
        ],
        'email' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino']['alias'],
            'inputType'               => 'text',
            'required'                => true,
            'config'                  => [
                'prefix' => \Contao\Environment::get('base') . '../casino/'
            ],
            'eval'                    => array('mandatory'=>true, 'unique'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ]
	)
);


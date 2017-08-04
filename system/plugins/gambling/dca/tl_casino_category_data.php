<?php

/**
 * Table tl_casino_category_data
 */
$GLOBALS['TL_DCA']['tl_casino_category_data'] = array
(

	// Config
	'config' => [
		'dataContainer'               => 'Table',
		'sql' => [
			'keys' => [
				'id' => 'primary'
			]
		]
	],

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
        'pid' => [
            'sql'                     => "int(10) unsigned NOT NULL"
        ],
        'country' => [
            'sql'                     => "int(10) unsigned NOT NULL"
        ],
        'sorting' => [
            'sql'               => "int(10) unsigned NOT NULL default '0'"
        ],
	)
);
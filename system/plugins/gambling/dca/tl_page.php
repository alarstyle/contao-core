<?php

$GLOBALS['TL_DCA']['tl_page']['fields']['navigationTitle'] = [
    'label'     => ['Navigation title', ''],
    'inputType' => 'text',
	'sql'       => "blob NULL",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['metaTitle'] = [
    'label'     => ['Meta title', ''],
    'inputType' => 'text',
	'sql'       => "blob NULL",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['metaDescription'] = [
    'label'     => ['Meta description', ''],
    'inputType' => 'textarea',
	'sql'       => "blob NULL",
];

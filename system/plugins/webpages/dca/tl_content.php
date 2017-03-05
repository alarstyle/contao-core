<?php

$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][]     = ['Grow\Webpages\Pattern', 'getVariables'];
$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][]   = ['Grow\Webpages\Pattern', 'saveVariables'];
$GLOBALS['TL_DCA']['tl_content']['fields']['pattern_data']          = [
	'label'     => &$GLOBALS['TL_LANG']['tl_content']['pattern_data'],
	'exclude'   => true,
	'inputType' => 'hidden',
    'eval'      => array('tl_class' => 'hidden'),
	'sql'       => "mediumblob NULL",
	'save_callback' => array(
		array('Carbid\Pattern', 'saveData'),
	),
];

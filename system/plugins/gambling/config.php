<?php

if (TL_MODE == 'BE') {
    $GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('Gambling\\BackendHook', 'parseBackendTemplate');
}

$GLOBALS['TL_CSS'][] = '/system/plugins/gambling/assets/css/main.css';


array_insert_assoc($GLOBALS['NAVIGATION'], -1, 'countries', [
    'label' => 'Languages & Countries',
    'controller' => 'Grow\\Controllers\\GroupsEditing',
    'config' => [
        'group' => [
            'table' => 'tl_country',
            'title' => 'Countries list',
            'newLabel' => 'Add Country'
        ],
    ]
]);
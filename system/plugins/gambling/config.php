<?php

if (TL_MODE == 'BE') {
    $GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('Gambling\\BackendHook', 'parseBackendTemplate');
}

$GLOBALS['TL_CSS'][] = '/system/plugins/gambling/assets/css/main.css';
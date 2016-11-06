<?php

\Contao\TemplateLoader::addFiles([
    'be_filemanager' => 'system/plugins/filemanager/templates',
]);

$GLOBALS['BE_MOD']['system']['filemanager'] = ['callback' => 'Contao\\FileManager'];

$GLOBALS['TL_CSS'][] = '/system/plugins/filemanager/assets/css/filemanager.css';

$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/filemanager/assets/js/components/rc-filemanager.js';

$GLOBALS['TL_JS_TEMPLATES']['file-manager'] = '/system/plugins/filemanager/assets/js/components/rc-filemanager.html';
$GLOBALS['TL_JS_TEMPLATES']['file-manager-table'] = '/system/plugins/filemanager/assets/js/components/rc-filemanager.html';


//$GLOBALS['RC_ROUTES']['filemanager'] = 'Contao\\FileManager';
$GLOBALS['RC_ROUTES']['filemanager'] = 'Contao\\Controllers\\FileManagerController';


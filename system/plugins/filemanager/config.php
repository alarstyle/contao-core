<?php

\Contao\TemplateLoader::addFiles([
    'be_filemanager' => 'system/plugins/filemanager/templates',
]);


/**
 * Back end form fields
 */
$GLOBALS['BE_FFL']['filePicker'] = 'Raccoon\\Editors\\FilePicker';


$GLOBALS['BE_MOD']['system']['filemanager'] = ['callback' => 'Contao\\FileManager'];

$GLOBALS['TL_CSS'][] = '/system/plugins/filemanager/assets/css/filemanager.css';

$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/filemanager/assets/js/components/rc-filemanager.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/filemanager/assets/js/components/rc-filepicker.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/filemanager/assets/js/components/rc-filepicker-modal.js';

$GLOBALS['TL_JS_TEMPLATES']['rc-filemanager-template'] = '/system/plugins/filemanager/assets/js/components/rc-filemanager.html';
$GLOBALS['TL_JS_TEMPLATES']['rc-filepicker-template'] = '/system/plugins/filemanager/assets/js/components/rc-filepicker.html';
$GLOBALS['TL_JS_TEMPLATES']['rc-filepicker-modal-template'] = '/system/plugins/filemanager/assets/js/components/rc-filepicker-modal.html';
//$GLOBALS['TL_JS_TEMPLATES']['file-manager-table'] = '/system/plugins/filemanager/assets/js/components/rc-filemanager.html';


$GLOBALS['RC_ROUTES']['filemanager'] = 'Contao\\Controllers\\FileManagerController';


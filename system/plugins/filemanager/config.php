<?php

\Contao\TemplateLoader::addFiles([
    'be_filemanager' => 'system/plugins/filemanager/templates',
]);

$GLOBALS['GLOBAL_ACTIONS']['filemanagerList'] = ['Grow\\Controllers\\FilemanagerController', 'ajaxList'];
$GLOBALS['GLOBAL_ACTIONS']['filemanagerUpload'] = ['Grow\\Controllers\\FilemanagerController', 'ajaxUpload'];
$GLOBALS['GLOBAL_ACTIONS']['filemanagerNewFolder'] = ['Grow\\Controllers\\FilemanagerController', 'ajaxNewFolder'];
$GLOBALS['GLOBAL_ACTIONS']['filemanagerRename'] = ['Grow\\Controllers\\FilemanagerController', 'ajaxRename'];
$GLOBALS['GLOBAL_ACTIONS']['filemanagerDelete'] = ['Grow\\Controllers\\FilemanagerController', 'ajaxDelete'];

/**
 * Back end form fields
 */
$GLOBALS['BE_FFL']['filePicker'] = 'Raccoon\\Editors\\FilePicker';


$GLOBALS['TL_CSS'][] = '/system/plugins/filemanager/assets/css/filemanager.css';


//$GLOBALS['RC_ROUTES']['filemanager'] = 'Contao\\Controllers\\FileManagerController';


$GLOBALS['COMPONENTS']['rc-filemanager'] = [
    '/system/plugins/filemanager/assets/js/components/rc-filemanager.js',
    '/system/plugins/filemanager/assets/js/components/rc-filemanager.html'
];

$GLOBALS['COMPONENTS']['rc-filepicker'] = [
    '/system/plugins/filemanager/assets/js/components/rc-filepicker.js',
    '/system/plugins/filemanager/assets/js/components/rc-filepicker.html'
];

$GLOBALS['COMPONENTS']['rc-filepicker-modal'] = [
    '/system/plugins/filemanager/assets/js/components/rc-filepicker-modal.js',
    '/system/plugins/filemanager/assets/js/components/rc-filepicker-modal.html'
];


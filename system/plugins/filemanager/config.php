<?php

\Contao\TemplateLoader::addFiles([
    'be_filemanager' => 'system/plugins/filemanager/templates',
]);

$GLOBALS['GLOBAL_ACTIONS']['filemanagerList'] = ['Grow\\Controllers\\FileManagerController', 'ajaxList'];
$GLOBALS['GLOBAL_ACTIONS']['filemanagerUpload'] = ['Grow\\Controllers\\FileManagerController', 'ajaxUpload'];
$GLOBALS['GLOBAL_ACTIONS']['filemanagerNewFolder'] = ['Grow\\Controllers\\FileManagerController', 'ajaxNewFolder'];
$GLOBALS['GLOBAL_ACTIONS']['filemanagerRename'] = ['Grow\\Controllers\\FileManagerController', 'ajaxRename'];
$GLOBALS['GLOBAL_ACTIONS']['filemanagerDelete'] = ['Grow\\Controllers\\FileManagerController', 'ajaxDelete'];


$GLOBALS['UNITS']['filePicker'] = 'Grow\\Units\\FilePicker';


$GLOBALS['TL_CSS'][] = '/system/plugins/filemanager/assets/css/filemanager.css';


$GLOBALS['COMPONENTS']['rc-filemanager'] = [
    '/system/plugins/filemanager/assets/js/components/rc-filemanager.js',
    '/system/plugins/filemanager/assets/js/components/rc-filemanager.html'
];

$GLOBALS['COMPONENTS']['unit-filepicker'] = [
    '/system/plugins/filemanager/assets/js/components/unit-filepicker.js',
    '/system/plugins/filemanager/assets/js/components/unit-filepicker.html'
];

$GLOBALS['COMPONENTS']['rc-filepicker-modal'] = [
    '/system/plugins/filemanager/assets/js/components/rc-filepicker-modal.js',
    '/system/plugins/filemanager/assets/js/components/rc-filepicker-modal.html'
];


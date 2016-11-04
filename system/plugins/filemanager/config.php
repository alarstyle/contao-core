<?php

\Contao\TemplateLoader::addFiles([
    'be_filemanager' => 'system/plugins/filemanager/templates',
]);

$GLOBALS['BE_MOD']['system']['files2'] = ['callback' => 'Contao\\FileManager'];

$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/filemanager/assets/js/components/file-manager.js';

$GLOBALS['TL_JS_TEMPLATES']['file-manager'] = '/system/plugins/filemanager/assets/js/components/file-manager.html';
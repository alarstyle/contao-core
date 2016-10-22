<?php

\Contao\TemplateLoader::addFiles([
    'be_filemanager' => 'system/plugins/backend-filemanager/templates',
]);

$GLOBALS['BE_MOD']['system']['files2'] = ['callback' => 'Contao\\FileManager'];
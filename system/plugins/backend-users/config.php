<?php

use Contao\BackendSections;

\Contao\TemplateLoader::addFiles([
    'be_user_header_menu'    => 'system/plugins/backend-users/templates',
]);


BackendSections::add('HEADER_MENU', function() {
    $template = new \Contao\BackendTemplate('be_user_header_menu');
    return $template->parse();
});
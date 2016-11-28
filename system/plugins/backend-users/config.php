<?php

use Contao\BackendSections;

\Contao\TemplateLoader::addFiles([
    'be_user_header_menu'    => 'system/plugins/backend-users/templates',
]);


BackendSections::add('HEADER_MENU', function() {
    $template = new \Contao\BackendTemplate('be_user_header_menu');
    $user = \Contao\BackendUser::getInstance();

    $template->name = $user->name;
    $template->avatar = $user->avatar ? 'style="background-image: url(\'' . $user->avatar .'\')"' : '';

    $template->logoutUrl = \Contao\Environment::get('base') . ltrim(\Contao\Config::get('backendUri'), '/');
    $template->logoutLabel = \Contao\Lang::get('MSC::logoutBT');

    return $template->parse();
});
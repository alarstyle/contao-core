<?php

if (TL_MODE == 'BE') {
    $GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('Gambling\\BackendHook', 'parseBackendTemplate');
}

$GLOBALS['TL_HOOKS']['beforeGetPageIdFromUrl'][] = array('Gambling\\FrontendHook', 'beforeGetPageIdFromUrl');
$GLOBALS['TL_HOOKS']['getPageUri'][] = array('Gambling\\FrontendHook', 'getPageUri');
$GLOBALS['TL_HOOKS']['getPageById'][] = array('Gambling\\FrontendHook', 'getPageById');
$GLOBALS['TL_HOOKS']['generatePage'][] = array('Gambling\\FrontendHook', 'generatePage');

/**
 * Frontend only
 */
if (TL_MODE == 'FE') {
    $GLOBALS['TL_HOOKS']['initializeSystem'][] = array('Gambling\\FrontendHook', 'initializeSystem');
}

$GLOBALS['TL_MODELS']['tl_country'] = 'Gambling\\Models\\CountryModel';
$GLOBALS['TL_MODELS']['tl_casino_category'] = 'Gambling\\Models\\CasinoCategoryModel';
$GLOBALS['TL_MODELS']['tl_casino'] = 'Gambling\\Models\\CasinoModel';
$GLOBALS['TL_MODELS']['tl_post'] = 'Gambling\\Models\\PostModel';

\Contao\TemplateLoader::addFiles([
    'be_posts'    => 'system/plugins/gambling/templates',
    'be_casinos'  => 'system/plugins/gambling/templates',
    'be_pages'  => 'system/plugins/gambling/templates',
    'be_translations'  => 'system/plugins/gambling/templates',
    'be_options'  => 'system/plugins/gambling/templates',
    'mod_posts'  => 'system/plugins/gambling/templates'
]);

$GLOBALS['FE_MOD'] = [
    'stuff' => [
        'posts' => 'Gambling\\Modules\\PostsModule'
    ]
];

$GLOBALS['TL_CSS'][] = '/system/plugins/gambling/assets/css/main.css';


array_insert_assoc($GLOBALS['NAVIGATION'], 1, 'casinos', [
    'label' => 'Casinos',
    'controller' => 'Gambling\\Controllers\\Casinos',
    'config' => [
        'group' => [
            'table' => 'tl_casino_category',
            'title' => 'Categories list',
            'labelAll' => 'All Casinos',
            'labelNew' => 'Add New Category',
            'creatable' => true,
            'editable' => true,
            'labelCallback' => function ($item) {
                return $item->name;
            },
            'titleCallback' => function ($item) {
                return $item->name;
            }
        ],
        'list' => [
            'table' => 'tl_casino',
            'title' => 'Casinos',
            'labelNew' => 'Add New Casino',
            'labelEdit' => 'Edit Casino',
            'creatable' => true,
            'order' => [['casino_sorting', 'desc'], ['tl_casino.id', 'desc']],
            'headersCallback' => function($headers) {
                foreach($headers as &$header) {
                    switch($header['name']) {
                        case 'img_logo':
                            $header['label'] = '';
                    }
                }
                return $headers;
            },
            'listCallback' => function($list) {
                foreach ($list as $i=>$item) {
                    $list[$i]['fields'][0] = '<div class="item-img" style="background-image: url(\'' . $list[$i]['fields'][0] . '\')"></div>';
                    $countriesIds = deserialize($list[$i]['fields'][2]);
                    if (empty($countriesIds)) {
                        $list[$i]['fields'][2] = 'None';
                    }
                    else {
                        $list[$i]['fields'][2] = \Gambling\BackendHelpers::getCountriesFlagsByIds($countriesIds);
                    }
                }
                return $list;
            },
        ]
    ]
]);

array_insert_assoc($GLOBALS['NAVIGATION'], 2, 'bettings', [
    'label' => 'Bettings',
    'controller' => 'Gambling\\Controllers\\Bettings',
    'config' => [
        'group' => [
            'table' => 'tl_casino_category',
            'title' => 'Categories list',
            'labelAll' => 'All Bettings',
            'labelNew' => 'Add New Category',
            'creatable' => true,
            'editable' => true,
            'labelCallback' => function ($item) {
                return $item->name;
            },
            'titleCallback' => function ($item) {
                return $item->name;
            }
        ],
        'list' => [
            'table' => 'tl_casino',
            'title' => 'Bettings',
            'labelNew' => 'Add New Betting',
            'labelEdit' => 'Edit Betting',
            'creatable' => true,
            'order' => [['tl_casino.id', 'desc']],
            'headersCallback' => function($headers) {
                foreach($headers as &$header) {
                    switch($header['name']) {
                        case 'img_logo':
                            $header['label'] = '';
                    }
                }
                return $headers;
            },
            'listCallback' => function($list) {
                foreach ($list as $i=>$item) {
                    $list[$i]['fields'][0] = '<div class="item-img" style="background-image: url(\'' . $list[$i]['fields'][0] . '\')"></div>';
                    $countriesIds = deserialize($list[$i]['fields'][2]);
                    if (empty($countriesIds)) {
                        $list[$i]['fields'][2] = 'None';
                    }
                    else {
                        $list[$i]['fields'][2] = \Gambling\BackendHelpers::getCountriesFlagsByIds($countriesIds);
                    }
                }
                return $list;
            },
        ]
    ]
]);


array_insert_assoc($GLOBALS['NAVIGATION'], 3, 'articles', [
    'label' => 'Articles',
    'controller' => 'Gambling\\Controllers\\Posts',
    'config' => [
        'group' => [
            'table' => 'tl_post_category',
            'title' => 'Categories',
            'labelAll' => 'All Articles',
            'labelNew' => 'Add New Category',
            'creatable' => false,
            'editable' => true
        ],
        'list' => [
            'table' => 'tl_post',
            'title' => 'Articles',
            'labelNew' => 'Add New Article',
            'labelEdit' => 'Edit Article',
            'creatable' => true,
            'order' => [['date', 'desc']],
            'headersCallback' => function($headers) {
                foreach($headers as &$header) {
                    switch($header['name']) {
                        case 'img_preview':
                            $header['label'] = '';
                    }
                }
                return $headers;
            },
            'listCallback' => function($list) {
                foreach ($list as $i=>$item) {
                    $list[$i]['fields'][0] = '<div class="item-img" style="background-image: url(\'' . $list[$i]['fields'][0] . '\')"></div>';
                    $countriesIds = deserialize($list[$i]['fields'][3]);
                    if (empty($countriesIds)) continue;
                    $list[$i]['fields'][3] = \Gambling\BackendHelpers::getCountriesFlagsByIds($countriesIds);
                }
                return $list;
            },
            'whereCallback' => function() {
                $groupId = \Contao\Input::post('groupId');
                if (empty($groupId) || $groupId === 'all') {
                    return [];
                }
                return [['category', $groupId]];
            },
        ]
    ]
]);


array_insert_assoc($GLOBALS['NAVIGATION'], -1, 'countries', [
    'label' => 'Languages & Countries',
    'controller' => 'Gambling\\Controllers\\Countries',
    'config' => [
        'group' => [
            'table' => 'tl_country',
            'title' => 'Countries list',
            'labelNew' => 'Add Country',
            'labelCallback' => function ($item) {
                return \Contao\System::getCountriesWithFlags()[$item->country];
            },
            'titleCallback' => function ($item) {
                return \Contao\System::getCountries()[$item->country];
            },
            'sortingCallback' => function ($groups) {
                usort($groups, function ($a, $b) {
                    if ($a['title'] === $b['title']) {
                        return 0;
                    }
                    return ($a['title'] < $b['title']) ? -1 : 1;
                });
                return $groups;
            }
        ],
    ]
]);



array_insert_assoc($GLOBALS['NAVIGATION'], 4, 'pages', [
    'label' => 'Website Structure',
    'controller' => 'Gambling\\Controllers\\Pages',
    'config' => [
        'group' => [
            'table' => 'tl_page',
            'title' => 'Pages',
            'creatable' => false,
            'editable' => false,
            'labelCallback' => function ($item) {
                return $item->name;
            },
            'titleCallback' => function ($item) {
                return $item->name;
            }
        ]
    ]
]);

array_insert_assoc($GLOBALS['NAVIGATION'], 5, 'casino_options', [
    'label' => 'Casino Options',
    'controller' => 'Gambling\\Controllers\\Options'
]);

array_insert_assoc($GLOBALS['NAVIGATION'], 6, 'translations', [
    'label' => 'Translations',
    'controller' => 'Gambling\\Controllers\\Translations'
]);

$GLOBALS['UNITS']['rating'] = 'Gambling\\Units\\Rating';
$GLOBALS['UNITS']['multilingualText'] = 'Gambling\\Units\\MultilingualText';


$GLOBALS['EDITOR_PRESETS']['regular']['content_css'] = '/themes/gambling/css/tinymce.css';
$GLOBALS['EDITOR_PRESETS']['regular']['body_class'] = 'text article-content';
<?php

if (TL_MODE == 'BE') {
    $GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('Gambling\\BackendHook', 'parseBackendTemplate');
}

\Contao\TemplateLoader::addFiles([
    'be_posts'    => 'system/plugins/gambling/templates'
]);

$GLOBALS['TL_CSS'][] = '/system/plugins/gambling/assets/css/main.css';


array_insert_assoc($GLOBALS['NAVIGATION'], 1, 'casinos', [
    'label' => 'Casinos',
    'controller' => 'Grow\\Controllers\\ListingWithGroups',
    'config' => [
        'group' => [
            'table' => 'tl_casino_category',
            'title' => 'Categories list',
            'labelAll' => 'All Casinos',
            'labelNew' => 'Add New Category',
            'creatable' => true,
            'editable' => true,
            'labelCallback' => function ($item) {
                return $item['name'];
            },
            'titleCallback' => function ($item) {
                return $item['name'];
            },
            //'sorting' => ['dateAdded DESC']
        ],
        'list' => [
            'table' => 'tl_casino',
            'title' => 'Casinos',
            'labelNew' => 'Add New Casino',
            'labelEdit' => 'Edit Casino',
            'creatable' => true
        ]
    ]
]);


array_insert_assoc($GLOBALS['NAVIGATION'], 2, 'articles', [
    'label' => 'Articles',
    'controller' => 'Gambling\\Controllers\\Posts',
    'config' => [
        'group' => [
            'table' => 'tl_post_category',
            'title' => 'Categories',
            'labelAll' => 'All Articles',
            'labelNew' => 'Add New Category',
            'creatable' => true,
            'editable' => true,
            'labelCallback' => function ($item) {
                return $item['name'];
            },
            'titleCallback' => function ($item) {
                return $item['name'];
            }
        ],
        'list' => [
            'table' => 'tl_post',
            'title' => 'Articles',
            'labelNew' => 'Add New Article',
            'labelEdit' => 'Edit Article',
            'creatable' => true,
            'order' => 'date DESC',
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
                    return '';
                }
                return 'category = ' . $groupId;
            },
        ]
    ]
]);


array_insert_assoc($GLOBALS['NAVIGATION'], -1, 'countries', [
    'label' => 'Languages & Countries',
    'controller' => 'Grow\\Controllers\\GroupsEditing',
    'config' => [
        'group' => [
            'table' => 'tl_country',
            'title' => 'Countries list',
            'labelNew' => 'Add Country',
            'labelCallback' => function ($item) {
                return \Contao\System::getCountriesWithFlags()[$item['country']];
            },
            'titleCallback' => function ($item) {
                return \Contao\System::getCountries()[$item['country']];
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


$GLOBALS['COMPONENTS']['unit-rating'] = [
    '/system/plugins/gambling/assets/js/components/unit-rating.js',
    '/system/plugins/gambling/assets/js/components/unit-rating.html'
];


$GLOBALS['UNITS']['rating'] = 'Gambling\\Units\\Rating';
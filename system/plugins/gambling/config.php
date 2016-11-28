<?php

if (TL_MODE == 'BE') {
    $GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('Gambling\\BackendHook', 'parseBackendTemplate');
}

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

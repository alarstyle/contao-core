<?php

return [

//    'routes' => function($route) {
//        $route->get('go1/{casinoAlias}', function() {
//
//        });
//
//        $route->get('go2/{casinoAlias}', function() {
//
//        });
//    },

    'routes' => [
        [
            'pattern' => 'go1/{casinoAlias}',
            'methods' => ['GET'],
            'handler' => ['\Gambling\Routes', 'casinoAffiliateLink']
        ],
        [
            'pattern' => 'go2/{casinoAlias}',
            'methods' => ['GET'],
            'handler' => ['\Gambling\Routes', 'bettingAffiliateLink']
        ]
    ],

    'components' => [
        'unit-rating' => [
            '/system/plugins/gambling/assets/js/components/unit-rating.js',
            '/system/plugins/gambling/assets/js/components/unit-rating.html'
        ],
        'unit-multilingual-text' => [
            '/system/plugins/gambling/assets/js/components/unit-multilingual-text.js',
            '/system/plugins/gambling/assets/js/components/unit-multilingual-text.html'
        ]
    ]

];
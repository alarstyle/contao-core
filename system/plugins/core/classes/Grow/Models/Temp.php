<?php

namespace Grow\Models;

class Temp extends Model
{
    protected $columns = [
        'countries' => 'binary',
        'name' => [
            'type' => 'string',
            'length' => 255,
            'nullable' => false,
            'default' => ''
        ],
        'url' => [
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'owner' => [
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'year' => [
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'licence' => [
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'phone' => [
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'email' => [
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'rating' => [
            'sql'                     => "float NULL"
        ],
        'isCasino' => [
            'sql'               => "char(1) NOT NULL default ''"
        ],
        'isBetting' => [
            'sql'               => "char(1) NOT NULL default ''"
        ],
        'casino_link' => array
        (
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'betting_link' => array
        (
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'casino_categories' => array
        (
            'sql'                     => "blob NULL"
        ),
        'betting_categories' => array
        (
            'sql'                     => "blob NULL"
        ),
        'img_logo' => [
            'sql'               => "varchar(255) NOT NULL default ''"
        ],
        'img_cover' => [
            'sql'               => "varchar(255) NOT NULL default ''"
        ]
    ];


    protected function registerColumns()
    {
        $this->columns = [
            'countries' => [
                'sql'                     => "blob NULL"
            ],
            'name' => array
            (
                'sql'                     => "varchar(255) NOT NULL default ''"
            ),
            'url' => [
                'sql'                     => "varchar(255) NOT NULL default ''"
            ],
            'owner' => [
                'sql'                     => "varchar(255) NOT NULL default ''"
            ],
            'year' => [
                'sql'                     => "varchar(255) NOT NULL default ''"
            ],
            'licence' => [
                'sql'                     => "varchar(255) NOT NULL default ''"
            ],
            'phone' => [
                'sql'                     => "varchar(255) NOT NULL default ''"
            ],
            'email' => [
                'sql'                     => "varchar(255) NOT NULL default ''"
            ],
            'rating' => [
                'sql'                     => "float NULL"
            ],
            'isCasino' => [
                'sql'               => "char(1) NOT NULL default ''"
            ],
            'isBetting' => [
                'sql'               => "char(1) NOT NULL default ''"
            ],
            'casino_link' => array
            (
                'sql'                     => "varchar(255) NOT NULL default ''"
            ),
            'betting_link' => array
            (
                'sql'                     => "varchar(255) NOT NULL default ''"
            ),
            'casino_categories' => array
            (
                'sql'                     => "blob NULL"
            ),
            'betting_categories' => array
            (
                'sql'                     => "blob NULL"
            ),
            'img_logo' => [
                'sql'               => "varchar(255) NOT NULL default ''"
            ],
            'img_cover' => [
                'sql'               => "varchar(255) NOT NULL default ''"
            ]
        ];
    }


}

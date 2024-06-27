<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Versatile Search',
    'description' => 'A versatile and extendable search extension',
    'version' => '3.0.0',
    'category' => 'plugin',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'frontend' => '12.4.0-12.4.99',
            'indexed_search' => '12.4.0-12.4.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'author' => 'Thorben Nissen',
    'author_email' => 'thorben@webcoast.dk',
    'author_company' => '',
    'autoload' => [
        'psr-4' => [
            'WEBcoast\\VersatileSearch\\' => 'Classes'
        ]
    ],
];

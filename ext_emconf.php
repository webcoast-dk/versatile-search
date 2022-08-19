<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Versatile Search',
    'description' => 'A versatile and extendable search extension',
    'version' => '2.0.0',
    'category' => 'plugin',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99',
            'frontend' => '10.4.0-11.5.99',
            'indexed_search' => '10.4.0-11.5.99'
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

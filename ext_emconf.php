<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Versatile Search',
    'description' => 'A versatile and extendable search extension',
    'category' => 'plugin',
    'author' => 'Thorben Nissen',
    'author_email' => 'thorben.nissen@kapp-hamburg.de',
    'author_company' => '',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'version' => '1.0.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'indexed_search' => '9.5.0-9.5.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];

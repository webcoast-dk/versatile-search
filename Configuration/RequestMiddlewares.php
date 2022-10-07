<?php

return [
    'frontend' => [
        'versatile-search/suggest' => [
            'target' => \WEBcoast\VersatileSearch\Middleware\SuggestMiddleware::class,
            'after' => [
                'typo3/cms-frontend/maintenance-mode'
            ],
            'before' => [
                'typo3/cms-frontend/backend-user-authentication'
            ]
        ]
    ]
];

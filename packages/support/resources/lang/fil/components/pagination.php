<?php

return [
    'label' => 'Navigation ng mga pahina',
    'overview' => '{1} Ipinapakita ang 1 resulta|[2,*] Ipinapakita ang :first hanggang :last sa :total resulta',
    'fields' => [
        'records_per_page' => [
            'label' => 'Bawat pahina',
            'options' => [
                'all' => 'Lahat',
            ],
        ],
    ],
    'actions' => [
        'first' => [
            'label' => 'Una',
        ],
        'go_to_page' => [
            'label' => 'Pumunta sa pahina :page',
        ],
        'last' => [
            'label' => 'Huli',
        ],
        'next' => [
            'label' => 'Susunod',
        ],
        'previous' => [
            'label' => 'Nauna',
        ],
    ],
];

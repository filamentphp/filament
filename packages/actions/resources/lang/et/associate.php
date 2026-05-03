<?php

return [
    'single' => [
        'label' => 'Ühenda',
        'modal' => [
            'heading' => 'Ühenda :label',
            'fields' => [
                'record_id' => [
                    'label' => 'Kirje',
                ],
            ],
            'actions' => [
                'associate' => [
                    'label' => 'Ühenda',
                ],
                'associate_another' => [
                    'label' => 'Ühenda & ühenda järgmine',
                ],
            ],
        ],
        'notifications' => [
            'associated' => [
                'title' => 'Ühendatud',
            ],
        ],
    ],
];

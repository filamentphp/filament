<?php

return [
    'single' => [
        'label' => 'Permanenteng i-delete',
        'modal' => [
            'heading' => 'Permanenteng i-delete ang :label',
            'actions' => [
                'delete' => [
                    'label' => 'I-delete',
                ],
            ],
        ],
        'notifications' => [
            'deleted' => [
                'title' => 'Na-delete na',
            ],
        ],
    ],
    'multiple' => [
        'label' => 'Permanenteng i-delete ang mga napili',
        'modal' => [
            'heading' => 'Permanenteng i-delete ang mga napiling :label',
            'actions' => [
                'delete' => [
                    'label' => 'I-delete',
                ],
            ],
        ],
        'notifications' => [
            'deleted' => [
                'title' => 'Na-delete na',
            ],
            'deleted_partial' => [
                'title' => 'Na-delete ang :count sa :total',
                'missing_authorization_failure_message' => 'Wala kang pahintulot na i-delete ang :count.',
                'missing_processing_failure_message' => 'Hindi ma-delete ang :count.',
            ],
            'deleted_none' => [
                'title' => 'Hindi na-delete',
                'missing_authorization_failure_message' => 'Wala kang pahintulot na i-delete ang :count.',
                'missing_processing_failure_message' => 'Hindi ma-delete ang :count.',
            ],
        ],
    ],
];

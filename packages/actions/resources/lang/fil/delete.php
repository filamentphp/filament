<?php

return [
    'single' => [
        'label' => 'I-delete',
        'modal' => [
            'heading' => 'I-delete ang :label',
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
        'label' => 'I-delete ang mga napili',
        'modal' => [
            'heading' => 'I-delete ang mga napiling :label',
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

<?php

return [

    'single' => [

        'label' => '복원',

        'modal' => [

            'heading' => ':label 복원',

            'actions' => [

                'restore' => [
                    'label' => '복원',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => '복원 완료',
            ],

        ],

    ],

    'multiple' => [

        'label' => '선택한 항목 복원',

        'modal' => [

            'heading' => '선택한 :label 복원',

            'actions' => [

                'restore' => [
                    'label' => '복원',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => '복원 완료',
            ],

            'restored_partial' => [
                'title' => ':total 중 :count개 복원됨',
                'missing_authorization_failure_message' => ':count개를 복원할 권한이 없습니다.',
                'missing_processing_failure_message' => ':count개를 복원할 수 없습니다.',
            ],

            'restored_none' => [
                'title' => '복원 실패',
                'missing_authorization_failure_message' => ':count개를 복원할 권한이 없습니다.',
                'missing_processing_failure_message' => ':count개를 복원할 수 없습니다.',
            ],

        ],

    ],

];

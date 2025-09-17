<?php

return [

    'single' => [

        'label' => '영구 삭제',

        'modal' => [

            'heading' => ':label 영구 삭제',

            'actions' => [

                'delete' => [
                    'label' => '삭제',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '삭제 완료',
            ],

        ],

    ],

    'multiple' => [

        'label' => '선택한 항목 영구 삭제',

        'modal' => [

            'heading' => '선택한 :label 영구 삭제',

            'actions' => [

                'delete' => [
                    'label' => '삭제',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '삭제 완료',
            ],

            'deleted_partial' => [
                'title' => ':total 중 :count개 삭제됨',
                'missing_authorization_failure_message' => ':count개를 삭제할 권한이 없습니다.',
                'missing_processing_failure_message' => ':count개를 삭제할 수 없습니다.',
            ],

            'deleted_none' => [
                'title' => '삭제 실패',
                'missing_authorization_failure_message' => ':count개를 삭제할 권한이 없습니다.',
                'missing_processing_failure_message' => ':count개를 삭제할 수 없습니다.',
            ],

        ],

    ],

];

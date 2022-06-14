<?php

return [

    'breadcrumb' => '목록',

    'actions' => [

        'create' => [

            'label' => '새로운 :label 만들기',

            'modal' => [

                'heading' => ':label 만들기',

                'actions' => [

                    'create' => [
                        'label' => '만들기',
                    ],

                    'create_and_create_another' => [
                        'label' => '계속 만들기',
                    ],

                ],

            ],

            'messages' => [
                'created' => '생성 완료',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => '삭제',

                'messages' => [
                    'deleted' => '삭제 완료',
                ],

            ],

            'edit' => [

                'label' => '수정',

                'modal' => [

                    'heading' => '수정 :label',

                    'actions' => [

                        'save' => [
                            'label' => '저장',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => '저장 완료',
                ],

            ],

            'view' => [
                'label' => '보기',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => '선택한 항목 삭제',

                'messages' => [
                    'deleted' => '삭제 완료',
                ],

            ],

        ],

    ],

];

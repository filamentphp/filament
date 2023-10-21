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

        ],

    ],

];

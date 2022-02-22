<?php

return [

    'title' => ':label 수정',

    'breadcrumb' => '수정',

    'actions' => [

        'delete' => [

            'label' => '삭제',

            'modal' => [

                'heading' => ':label 삭제',

                'subheading' => '이 항목을 삭제하시겠습니까?',

                'buttons' => [

                    'delete' => [
                        'label' => '삭제',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => '삭제 완료',
            ],

        ],

        'view' => [
            'label' => '보기',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => '취소',
            ],

            'save' => [
                'label' => '저장',
            ],

        ],

    ],

    'messages' => [
        'saved' => '저장 완료',
    ],

];

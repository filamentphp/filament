<?php

return [

    'title' => '编辑 :label',

    'breadcrumb' => '编辑',

    'actions' => [

        'delete' => [

            'label' => '删除',

            'modal' => [

                'heading' => '删除 :label',

                'subheading' => '您确定要这样做吗？',

                'buttons' => [

                    'delete' => [
                        'label' => '删除',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => '已删除',
            ],

        ],

        'view' => [
            'label' => '查看',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => '取消',
            ],

            'save' => [
                'label' => '保存',
            ],

        ],

    ],

    'messages' => [
        'saved' => '已保存',
    ],

];

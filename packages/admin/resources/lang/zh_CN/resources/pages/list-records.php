<?php

return [

    'breadcrumb' => '列表',

    'actions' => [

        'create' => [

            'label' => '新建 :label',

            'modal' => [

                'heading' => '创建 :label',

                'actions' => [

                    'create' => [
                        'label' => '创建',
                    ],

                    'create_and_create_another' => [
                        'label' => '保存并创建另一个',
                    ],

                ],

            ],

            'messages' => [
                'created' => '已保存',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => '删除',

                'messages' => [
                    'deleted' => '已删除',
                ],

            ],

            'edit' => [

                'label' => '编辑',

                'modal' => [

                    'heading' => '编辑 :label',

                    'actions' => [

                        'save' => [
                            'label' => '保存',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => '已保存',
                ],

            ],

            'view' => [
                'label' => '查看',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => '删除已选项目',

                'messages' => [
                    'deleted' => '已删除',
                ],

            ],

        ],

    ],

];

<?php

return [

    'buttons' => [

        'attach' => [
            'label' => '绑定',
        ],

        'create' => [
            'label' => '添加',
        ],

        'detach' => [
            'label' => '解绑所选',
        ],

    ],

    'modals' => [

        'attach' => [

            'buttons' => [

                'cancel' => [
                    'label' => '取消',
                ],

                'attach' => [
                    'label' => '绑定',
                ],

                'attachAnother' => [
                    'label' => '绑定 & 绑定下一个',
                ],

            ],

            'form' => [

                'related' => [
                    'placeholder' => '搜索...',
                ],

            ],

            'heading' => '绑定',

            'messages' => [
                'attached' => '绑定成功!',
            ],

        ],

        'create' => [

            'buttons' => [

                'cancel' => [
                    'label' => '取消',
                ],

                'create' => [
                    'label' => '添加',
                ],

                'createAnother' => [
                    'label' => '添加 & 添加下一个',
                ],

            ],

            'heading' => '添加',

            'messages' => [
                'created' => '添加成功!',
            ],

        ],

        'detach' => [

            'buttons' => [

                'cancel' => [
                    'label' => '取消',
                ],

                'detach' => [
                    'label' => '解绑所选',
                ],

            ],

            'description' => '您确定要解绑选定的记录吗?这一操作不能取消.',

            'heading' => '解绑所选的数据? ',

            'messages' => [
                'detached' => '解绑成功!',
            ],

        ],

        'edit' => [

            'buttons' => [

                'cancel' => [
                    'label' => '取消',
                ],

                'save' => [
                    'label' => '保存',
                ],

            ],

            'heading' => '编辑',

            'messages' => [
                'saved' => '保存成功!',
            ],

        ],

    ],

];

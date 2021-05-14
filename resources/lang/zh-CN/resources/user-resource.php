<?php

return [

    'form' => [

        'avatar' => [
            'label' => '头像',
        ],

        'email' => [
            'label' => '邮箱地址',
        ],

        'isAdmin' => [
            'label' => '是否为Filament管理员?',
            'helpMessage' => 'Filament管理员能够访问的所有页面，并管理其他用户.',
        ],

        'isUser' => [
            'label' => '是否为Filament用户?',
        ],

        'name' => [
            'label' => '用户名',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => '密码',
                    'edit' => '设置一个新密码',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => '密码',
                ],

                'passwordConfirmation' => [
                    'label' => '确认密码',
                ],

            ],

        ],

        'roles' => [
            'label' => '角色',
            'placeholder' => '选择一个角色',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => '邮箱地址',
            ],

            'name' => [
                'label' => '用户名',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => '管理员',
            ],

        ],

    ],

];

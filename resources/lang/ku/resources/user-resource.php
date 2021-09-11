<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'وێنە',
        ],

        'email' => [
            'label' => 'ئیمەیڵ',
        ],

        'isAdmin' => [
            'label' => 'بەڕێوەبەر Filament؟',
            'helpMessage' => 'بەڕێوەبەر دەتوانێت دەستی بگات بە هەموو بەشەکان بە بەکارهێنەرەکانیشەوە',
        ],

        'isUser' => [
            'label' => 'بەکارهێنەر Filament؟',
        ],

        'name' => [
            'label' => 'ناو',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'ووشەی نهێنی',
                    'edit' => 'دانانی ووشەی نهێنی',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'ووشەی نهێنی',
                ],

                'passwordConfirmation' => [
                    'label' => 'دڵنیابونەوە لە ووشەی نهێنی',
                ],

            ],

        ],

        'roles' => [
            'label' => 'ئاستەکانی بەڕێوەبردن',
            'placeholder' => 'ئاستێک هەڵبژێرە',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'ئیمەیڵ',
            ],

            'name' => [
                'label' => 'ناو',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'بەڕێوەبەرەکان',
            ],

        ],

    ],

];

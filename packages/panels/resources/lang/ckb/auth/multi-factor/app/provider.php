<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'بەرنامەی ڕەسەنایەتی',

            'below_content' => 'بەرنامەیەکی پارێزراو بەکاربهێنە بۆ دروستکردنی کۆدێکی کاتی بۆ پشتڕاستکردنەوەی چوونەژوورەوە.',

            'messages' => [
                'enabled' => 'چالاکە',
                'disabled' => 'ناچالاکە',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'کۆدێک لە بەرنامەی ڕەسەنایەتییەوە بەکاربهێنە',

        'code' => [

            'label' => 'کۆدی 6 ژمارەیی لە بەرنامەی ڕەسەنایەتی داخڵ بکە',

            'validation_attribute' => 'کۆد',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'لەبری ئەوە کۆدی گەڕاندنەوە بەکاربهێنە',
                ],

            ],

            'messages' => [

                'invalid' => 'کۆدی هەڵە داخڵکراوە',

            ],

        ],

        'recovery_code' => [

            'label' => 'یان, کۆدی گەڕاندنەوە داخڵ بکە',

            'validation_attribute' => 'کۆدی گەڕاندنەوە',

            'messages' => [

                'invalid' => 'کۆدی گەڕاندنەوە هەڵەیە',

            ],

        ],

    ],

];

<?php

return [

    'single' => [

        'label' => 'سڕینەوەی بەزۆر',

        'modal' => [

            'heading' => 'سڕینەوەی بەزۆری :label',

            'actions' => [

                'delete' => [
                    'label' => 'سڕینەوە',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'سڕایەوە',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'سڕینەوەی بەزۆری هەڵبژێردراوەکان',

        'modal' => [

            'heading' => 'سڕینەوەی بەزۆری هەڵبژێردراوەکانی :label',

            'actions' => [

                'delete' => [
                    'label' => 'سڕینەوە',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'سڕایەوە',
            ],

            'deleted_partial' => [
                'title' => ':count لە :total سڕدرایەوە',
                'missing_authorization_failure_message' => 'مۆڵەتت نییە بۆ سڕینەوەی :count.',
                'missing_processing_failure_message' => ':count نەتوانرا بسڕدرێتەوە.',
            ],

            'deleted_none' => [
                'title' => 'سڕینەوە سەرکەوتوو نەبوو',
                'missing_authorization_failure_message' => 'مۆڵەتت نییە بۆ سڕینەوەی :count.',
                'missing_processing_failure_message' => ':count نەتوانرا بسڕدرێتەوە.',
            ],

        ],

    ],

];

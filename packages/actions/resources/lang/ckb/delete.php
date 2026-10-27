<?php

return [

    'single' => [

        'label' => 'سڕینەوە',

        'modal' => [

            'heading' => 'سڕینەوەی :label',

            'actions' => [

                'delete' => [
                    'label' => 'سڕینەوە',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'سڕدرایەوە',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'سڕینەوەی هەڵبژێردراوەکان',

        'modal' => [

            'heading' => 'سڕینەوەی هەڵبژێردراوەکانی :label',

            'actions' => [

                'delete' => [
                    'label' => 'سڕینەوە',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'سڕدرایەوە',
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

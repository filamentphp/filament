<?php

return [

    'single' => [

        'label' => 'گەڕاندنەوە',

        'modal' => [

            'heading' => 'گەڕاندنەوەی :label',

            'actions' => [

                'restore' => [
                    'label' => 'گەڕاندنەوە',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'گەڕێندراوەیەوە',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'گەڕاندنەوەی هەڵبژێردراوەکان',

        'modal' => [

            'heading' => 'گەڕاندنەوەی هەڵبژێردراوەکانی :label',

            'actions' => [

                'restore' => [
                    'label' => 'گەڕاندنەوە',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'گەڕێندراوەیەوە',
            ],

            'restored_partial' => [
                'title' => ':count لە :total گەڕێندرایەوە',
                'missing_authorization_failure_message' => 'مۆڵەتت نییە بۆ گەڕاندنەوەی :count.',
                'missing_processing_failure_message' => ':count نەتوانرا بگەڕێندرێتەوە.',
            ],

            'restored_none' => [
                'title' => 'گەڕاندنەوە سەرکەوتوو نەبوو',
                'missing_authorization_failure_message' => 'مۆڵەتت نییە بۆ گەڕاندنەوەی :count.',
                'missing_processing_failure_message' => ':count نەتوانرا بگەڕێندرێتەوە.',
            ],

        ],

    ],

];

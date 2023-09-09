<?php

return [

    'label' => 'ڕێنوێیی پەڕەکردن',

    'overview' => '{1} پشاندانی ئەنجامێک|[2,*] پیشاندانی :first بۆ :last لە :total ئەنجام',

    'fields' => [

        'records_per_page' => [

            'label' => 'بۆ هەر پەڕەیەک',

            'options' => [
                'all' => 'هەموو',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'بڕۆ بۆ پەڕەی :page',
        ],

        'next' => [
            'label' => 'دواتر',
        ],

        'previous' => [
            'label' => 'پێشوو',
        ],

    ],

];

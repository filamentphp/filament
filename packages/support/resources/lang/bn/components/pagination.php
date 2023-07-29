<?php

return [

    'label' => 'পৃষ্ঠা সংখ্যাগুলো',

    'overview' => ':total এর, :first থেকে :last পর্যন্ত দেখানো হচ্ছে',

    'fields' => [

        'records_per_page' => [

            'label' => 'প্রতি পৃষ্ঠা',

            'options' => [
                'all' => 'সব',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => ':page পৃষ্টায় যান',
        ],

        'next' => [
            'label' => 'পরবর্তী',
        ],

        'previous' => [
            'label' => 'পূর্ববর্তী',
        ],

    ],

];

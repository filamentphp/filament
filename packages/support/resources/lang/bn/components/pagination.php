<?php

return [

    'label' => 'পৃষ্ঠা নেভিগেশন',

    'overview' => '{1} ১টি ফলাফল দেখানো হচ্ছে|[2,*] :total এর মধ্যে :first থেকে :last পর্যন্ত দেখানো হচ্ছে',

    'fields' => [

        'records_per_page' => [

            'label' => 'প্রতি পৃষ্ঠা',

            'options' => [
                'all' => 'সব',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'প্রথম',
        ],

        'go_to_page' => [
            'label' => ':page পৃষ্ঠায় যান',
        ],

        'last' => [
            'label' => 'শেষ',
        ],

        'next' => [
            'label' => 'পরবর্তী',
        ],

        'previous' => [
            'label' => 'পূর্ববর্তী',
        ],

    ],

];

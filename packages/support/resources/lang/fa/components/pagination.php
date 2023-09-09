<?php

return [

    'label' => 'صفحه بندی',

    'overview' => 'در حال نمایش :first به :last از :total نتیجه',

    'fields' => [

        'records_per_page' => [

            'label' => 'در هر صفحه',

            'options' => [
                'all' => 'همه',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'برو به صفحه :page',
        ],

        'next' => [
            'label' => 'بعدی',
        ],

        'previous' => [
            'label' => 'قبلی',
        ],

    ],

];

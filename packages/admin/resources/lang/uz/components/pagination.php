<?php

return [

    'label' => 'Paginatsiya navigatsiyasi',

    'overview' => '{1} 1 natija ko\'rsatilmoqda|[2,*] :first dan :last gacha :total natijalardan ko\'rsatilmoqda',

    'fields' => [

        'records_per_page' => [

            'label' => 'Sahifadagi yozuvlar',

            'options' => [
                'all' => 'Barchasi',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Sahifaga o\'tish :page',
        ],

        'next' => [
            'label' => 'Keyingi',
        ],

        'previous' => [
            'label' => 'Oldingi',
        ],

    ],

];
<?php

return [

    'label' => 'Sahifalar navigatsiyasi',

    'overview' => '{1} 1 natija ko\'rsatilmoqda |[2,*] :first dan :last gacha jami natijalar :total',

    'fields' => [

        'records_per_page' => [

            'label' => 'Har bir sahifaga',

            'options' => [
                'all' => 'Barchasi',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => ':page - sahifaga o\'tish',
        ],

        'next' => [
            'label' => 'Keyingi',
        ],

        'previous' => [
            'label' => 'Oldingi',
        ],

    ],

];

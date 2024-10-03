<?php

return [

    'label' => 'Sahifalar navigatsiyasi',

    'overview' => '{1} 1 natija ko\'rsatilmoqda |[2,*] :firstdan :lastgacha jami natijalar :totalta',

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
            'label' => ':pagechi sahifaga o\'tish',
        ],

        'next' => [
            'label' => 'Keyingi',
        ],

        'previous' => [
            'label' => 'Oldingi',
        ],

    ],

];

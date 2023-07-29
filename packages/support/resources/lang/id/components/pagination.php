<?php

return [

    'label' => 'Navigasi halaman',

    'overview' => 'Menampilkan :first sampai :last dari :total hasil',

    'fields' => [

        'records_per_page' => [

            'label' => 'per halaman',

            'options' => [
                'all' => 'Semua',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Ke halaman :page',
        ],

        'next' => [
            'label' => 'Selanjutnya',
        ],

        'previous' => [
            'label' => 'Sebelumnya',
        ],

    ],

];

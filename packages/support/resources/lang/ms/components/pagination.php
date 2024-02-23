<?php

return [

    'label' => 'Navigasi Penomboran',

    'overview' => 'Menunjukkan :first ke :last dari :total rekod',

    'fields' => [

        'records_per_page' => [

            'label' => 'setiap halaman',

            'options' => [
                'all' => 'Semua',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Pertama',
        ],

        'go_to_page' => [
            'label' => 'Pergi ke halaman :page',
        ],

        'last' => [
            'label' => 'Akhir',
        ],

        'next' => [
            'label' => 'Seterusnya',
        ],

        'previous' => [
            'label' => 'Sebelumnya',
        ],

    ],

];

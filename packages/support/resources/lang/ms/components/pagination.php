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

        'go_to_page' => [
            'label' => 'Pergi ke halaman :page',
        ],

        'next' => [
            'label' => 'Seterusnya',
        ],

        'previous' => [
            'label' => 'Sebelumnya',
        ],

    ],

];

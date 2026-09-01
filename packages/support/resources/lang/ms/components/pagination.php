<?php

return [

    'label' => 'Navigasi penomboran',

    'overview' => '{1} Memaparkan 1 rekod|[2,*] Memaparkan :first hingga :last daripada :total rekod',

    'fields' => [

        'records_per_page' => [

            'label' => 'per halaman',

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

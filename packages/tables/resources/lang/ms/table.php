<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Cari',
            'placeholder' => 'Carian',
        ],

    ],

    'pagination' => [

        'label' => 'Navigasi Penomboran',

        'overview' => 'Menunjukkan :first ke :last dari :total rekod',

        'fields' => [

            'records_per_page' => [
                'label' => 'setiap halaman',
            ],

        ],

        'buttons' => [

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

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Penapis',
        ],

        'open_actions' => [
            'label' => 'Tindakan terbuka',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Adakah anda pasti mahu melakukan ini?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Batal',
                ],

                'confirm' => [
                    'label' => 'Sahkan',
                ],

                'submit' => [
                    'label' => 'Hantar',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'Tiada rekod dijumpai',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Tetapkan semula penapis',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Semua',
        ],

        'select' => [
            'placeholder' => 'Semua',
        ],

    ],

    'selection_indicator' => [

        'selected_count' => '{1} 1 rekod dipilih.|[2,*] :count rekod yang dipilih.',

        'buttons' => [

            'select_all' => [
                'label' => 'Pilih semua :count',
            ],

            'deselect_all' => [
                'label' => 'Nyahpilih semua',
            ],

        ],

    ],

];

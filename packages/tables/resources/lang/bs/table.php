<?php

return [

    'columns' => [

        'color' => [

            'messages' => [
                'copied' => 'Kopirano',
            ],

        ],

        'tags' => [
            'more' => 'i :count više',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Pretraga',
            'placeholder' => 'Tražite',
        ],

    ],

    'pagination' => [

        'label' => 'Navigacija po stranicama',

        'overview' => 'Prikazivanje :first od :last od ukupno :total rezultata',

        'fields' => [

            'records_per_page' => [

                'label' => 'po stranici',

                'options' => [
                    'all' => 'Svi',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Idite na stranicu :page',
            ],

            'next' => [
                'label' => 'Dalje',
            ],

            'previous' => [
                'label' => 'Nazad',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Završite preuređivanje zapisa',
        ],

        'enable_reordering' => [
            'label' => 'Preuredite zapise',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'open_actions' => [
            'label' => 'Otvorene akcije',
        ],

        'toggle_columns' => [
            'label' => 'Preklopiti kolone',
        ],

    ],

    'empty' => [
        'heading' => 'Nije pronađen nijedan zapis',
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Sklonite filter',
            ],

            'remove_all' => [
                'label' => 'Sklonite svi filteri',
                'tooltip' => 'Sklonite svi filteri',
            ],

            'reset' => [
                'label' => 'Resetujete filters',
            ],

        ],

        'indicator' => 'Aktivne filteri',

        'multi_select' => [
            'placeholder' => 'Svi',
        ],

        'select' => [
            'placeholder' => 'Svi',
        ],

        'trashed' => [

            'label' => 'Izbriseni zapise',

            'only_trashed' => 'Samo izbrisani zapisi',

            'with_trashed' => 'Sa izbrisani zapisi',

            'without_trashed' => 'Bez izbrisani zapisi',

        ],

    ],

    'reorder_indicator' => 'Prevucite i ispustite zapise u red.',

    'selection_indicator' => [

        'selected_count' => '1 napis izabran.|:count napisi izabrani.',

        'buttons' => [

            'select_all' => [
                'label' => 'Izaberite sve :count',
            ],

            'deselect_all' => [
                'label' => 'Poništitite izbor',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortirajte po',
            ],

            'direction' => [

                'label' => 'Sortirajte po smjeru',

                'options' => [
                    'asc' => 'Uzlazno',
                    'desc' => 'Silazno',
                ],

            ],

        ],

    ],
];

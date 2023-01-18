<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'i :count više',
        ],

        'messages' => [
            'copied' => 'Kopirano',
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
            'label' => 'Završi preuređivanje zapisa',
        ],

        'enable_reordering' => [
            'label' => 'Preuredi zapise',
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
                'label' => 'Skloni filter',
            ],

            'remove_all' => [
                'label' => 'Skloni svi filteri',
                'tooltip' => 'Skloni svi filteri',
            ],

            'reset' => [
                'label' => 'Resetujte filtere',
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

            'label' => 'Izbrisani zapisi',

            'only_trashed' => 'Samo izbrisani zapisi',

            'with_trashed' => 'Sa izbrisanim zapisima',

            'without_trashed' => 'Bez izbrisanih zapisa',

        ],

    ],

    'reorder_indicator' => 'Prevucite i ispustite zapise u red.',

    'selection_indicator' => [

        'selected_count' => '1 izabran zapis.|:count izabrani zapisi.',

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

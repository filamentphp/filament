<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'ja :count lisää',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Aseta/poista massatoiminnon valinta kaikista kohteista.',
        ],

        'bulk_select_record' => [
            'label' => 'Aseta/poista massatoiminnon valinta kohteelle :key.',
        ],

        'search_query' => [
            'label' => 'Etsi',
            'placeholder' => 'Etsi',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Viimeistele tietueiden järjestely',
        ],

        'enable_reordering' => [
            'label' => 'Järjestele tietueita',
        ],

        'filter' => [
            'label' => 'Suodata',
        ],

        'open_bulk_actions' => [
            'label' => 'Avaa toiminnot',
        ],

        'toggle_columns' => [
            'label' => 'Näytä kolumnit',
        ],

    ],

    'empty' => [

        'heading' => 'Tietueita ei löytynyt',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Poista suodatin',
            ],

            'remove_all' => [
                'label' => 'Poista suodattimet',
                'tooltip' => 'Poista suodattimet',
            ],

            'reset' => [
                'label' => 'Tyhjennä suodattimet',
            ],

        ],

        'indicator' => 'Aktiiviset suodattimet',

        'multi_select' => [
            'placeholder' => 'Kaikki',
        ],

        'select' => [
            'placeholder' => 'Kaikki',
        ],

        'trashed' => [

            'label' => 'Poistetut tietueet',

            'only_trashed' => 'Vain poistetut tietueet',

            'with_trashed' => 'Poistettujen tietueiden kanssa',

            'without_trashed' => 'Ilman poistettuja tietueita',

        ],

    ],

    'reorder_indicator' => 'Raahaa ja pudota tietueet järjestykseen.',

    'selection_indicator' => [

        'selected_count' => '1 tietue valittu|:count tietuetta valittu',

        'actions' => [

            'select_all' => [
                'label' => 'Valitse kaikki :count tietuetta',
            ],

            'deselect_all' => [
                'label' => 'Poista valinta kaikista',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Järjestele',
            ],

            'direction' => [

                'label' => 'Järjestyksen suunta',

                'options' => [
                    'asc' => 'Nousevasti',
                    'desc' => 'Laskevasti',
                ],

            ],

        ],

    ],

];

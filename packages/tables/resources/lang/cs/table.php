<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Vyhledávání',
            'placeholder' => 'Hledat',
        ],

    ],

    'pagination' => [

        'label' => 'Stránkování',

        'overview' => 'Zobrazeno od :first do :last z :total záznamů',

        'fields' => [

            'records_per_page' => [
                'label' => 'na stránku',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Jít na stránku :page',
            ],

            'next' => [
                'label' => 'Další',
            ],

            'previous' => [
                'label' => 'Předchozí',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filtrovat',
        ],

        'open_actions' => [
            'label' => 'Otevřít panel akcí',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Opravdu chcete tuto akci provést?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Zrušit',
                ],

                'confirm' => [
                    'label' => 'Potvrdit',
                ],

                'submit' => [
                    'label' => 'Odeslat',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'Žádné záznamy nenalezeny',
    ],

    'selection_indicator' => [

        'selected_count' => '1 záznam zvolen.|:count záznamů zvoleno.',

        'buttons' => [

            'select_all' => [
                'label' => 'Označit všechny :count',
            ],

            'deselect_all' => [
                'label' => 'Odznačit všechny',
            ],

        ],

    ],

];

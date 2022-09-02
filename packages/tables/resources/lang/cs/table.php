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

        'toggle_columns' => [
            'label' => 'Skrýt/zobrazit sloupce',
        ],

    ],

    'empty' => [
        'heading' => 'Žádné záznamy nenalezeny',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Resetovat filtry',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Vše',
        ],

        'select' => [
            'placeholder' => 'Vše',
        ],

    ],

    'selection_indicator' => [

        'selected_count' => '{1} 1 záznam zvolen.|[2,4] :count záznamy zvoleny.|[5,*] :count záznamů zvoleno.',

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

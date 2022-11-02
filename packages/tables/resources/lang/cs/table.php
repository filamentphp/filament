<?php

return [

    'columns' => [

        'color' => [

            'messages' => [
                'copied' => 'Zkopírováno',
            ],

        ],

        'tags' => [
            'more' => 'a 1 další|a :count další| a :count dalších',
        ],

    ],

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

                'options' => [
                    'all' => 'Vše',
                ],
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

        'disable_reordering' => [
            'label' => 'Dokončit změnu pořadí položek',
        ],

        'enable_reordering' => [
            'label' => 'Změnit pořadí položek',
        ],

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

            'remove' => [
                'label' => 'Odstranit filtr',
            ],

            'remove_all' => [
                'label' => 'Odstranit všechny filtry',
                'tooltip' => 'Odstranit všechny filtry',
            ],

            'reset' => [
                'label' => 'Resetovat filtry',
            ],

        ],

        'indicator' => 'Aktivní filtry',

        'multi_select' => [
            'placeholder' => 'Vše',
        ],

        'select' => [
            'placeholder' => 'Vše',
        ],

        'trashed' => [

            'label' => 'Smazané položky',

            'only_trashed' => 'Pouze smazané položky',

            'with_trashed' => 'Včetně smazaných položek',

            'without_trashed' => 'Bez smazaných položek',

        ],

    ],

    'reorder_indicator' => 'Vyberte a přesuňte položky.',

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

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Seřadit podle',
            ],

            'direction' => [

                'label' => 'Směr řazení',

                'options' => [
                    'asc' => 'Vzestupně',
                    'desc' => 'Sestupně',
                ],

            ],

        ],

    ],

];

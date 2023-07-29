<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'a 1 další|a :count další| a :count dalších',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Vybrat/odznačit všechny položky pro hromadné akce.',
        ],

        'bulk_select_record' => [
            'label' => 'Vybrat/odznačit položku :key pro hromadné akce.',
        ],

        'search' => [
            'label' => 'Vyhledávání',
            'placeholder' => 'Hledat',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Dokončit změnu pořadí položek',
        ],

        'enable_reordering' => [
            'label' => 'Změnit pořadí položek',
        ],

        'filter' => [
            'label' => 'Filtrovat',
        ],

        'open_bulk_actions' => [
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

        'actions' => [

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

        'selected_count' => '{1} 1 záznam zvolen|[2,4] :count záznamy zvoleny|[5,*] :count záznamů zvoleno',

        'actions' => [

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

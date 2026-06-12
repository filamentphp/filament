<?php

return [

    'column_manager' => [

        'heading' => 'Sloupce',

        'actions' => [

            'apply' => [
                'label' => 'Použít sloupce',
            ],

            'reset' => [
                'label' => 'Resetovat sloupce',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Akce|Akce',
        ],

        'select' => [

            'loading_message' => 'Načítává se...',

            'no_options_message' => 'Nejsou dostupné žádné možnosti.',

            'no_search_results_message' => 'Žádné možnosti neodpovídají vašemu hledání.',

            'placeholder' => 'Vyberte možnost',

            'searching_message' => 'Vyhledává se...',

            'search_prompt' => 'Začněte psát pro vyhledávání...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Zobrazit o :count méně',
                'expand_list' => 'Zobrazit o :count více',
            ],

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

        'bulk_select_group' => [
            'label' => 'Vybrat/zrušit výběr skupiny :title pro hromadné akce.',
        ],

        'search' => [
            'label' => 'Vyhledávání',
            'placeholder' => 'Hledat',
            'indicator' => 'Hledat',
        ],

    ],

    'summary' => [

        'heading' => 'Shrnutí',

        'subheadings' => [
            'all' => 'Všechny :label',
            'group' => ':group shrnutí',
            'page' => 'Tato stránka',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Průměr',
            ],

            'count' => [
                'label' => 'Počet',
            ],

            'sum' => [
                'label' => 'Součet',
            ],

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

        'group' => [
            'label' => 'Seskupit',
        ],

        'open_bulk_actions' => [
            'label' => 'Otevřít panel akcí',
        ],

        'column_manager' => [
            'label' => 'Skrýt/zobrazit sloupce',
        ],

    ],

    'empty' => [

        'heading' => 'Žádné záznamy nenalezeny',

        'description' => 'Začněte vytvořením :modelu.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Použít filtry',
            ],

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

        'heading' => 'Filtrovat',

        'indicator' => 'Aktivní filtry',

        'multi_select' => [
            'placeholder' => 'Vše',
        ],

        'select' => [

            'placeholder' => 'Vše',

            'relationship' => [
                'empty_option_label' => 'Žádná',
            ],

        ],

        'trashed' => [

            'label' => 'Smazané položky',

            'only_trashed' => 'Pouze smazané položky',

            'with_trashed' => 'Včetně smazaných položek',

            'without_trashed' => 'Bez smazaných položek',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Seskupit podle',
            ],

            'direction' => [

                'label' => 'Směr seskupení',

                'options' => [
                    'asc' => 'Vzestupně',
                    'desc' => 'Sestupně',
                ],

            ],

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

    'default_model_label' => 'záznam',

];

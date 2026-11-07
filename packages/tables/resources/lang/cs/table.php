<?php

return [

    'column_manager' => [

        'heading' => 'Sloupce',

        'position' => 'Pozice :position z :count.',

        'position_in_group' => 'Pozice :position z :count ve skupině :group.',

        'reordered' => ':column je nyní na pozici :position z :count.',

        'reordered_deferred' => ':column je nyní na pozici :position z :count. Změny uložíte tlačítkem Použít sloupce.',

        'reordered_in_group' => ':column je nyní na pozici :position z :count ve skupině :group.',

        'reordered_in_group_deferred' => ':column je nyní na pozici :position z :count ve skupině :group. Změny uložíte tlačítkem Použít sloupce.',

        'actions' => [

            'apply' => [
                'label' => 'Použít sloupce',
            ],

            'move_up' => [
                'label' => 'Posunout :column nahoru',
            ],

            'move_down' => [
                'label' => 'Posunout :column dolů',
            ],

            'reorder' => [
                'label' => 'Přesunout sloupec',
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

        'editable' => [
            'saving' => 'Ukládá se změna...',
            'saved' => 'Změna uložena.',
            'failure' => 'Uložení se nepodařilo potvrdit. Před dalším pokusem obnovte stránku.',
            'unavailable' => 'Tuto položku už nelze upravit.',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Ano',
                'false' => 'Ne',
            ],

        ],

        'select' => [

            'loading_message' => 'Načítá se...',

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

            'more_list_items' => 'a 1 další|a :count další|a :count dalších',
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
            'group' => 'Shrnutí: :group',
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

        'reorder_record' => [
            'label' => 'Přesunout položku :key',
        ],

        'move_record_up' => [
            'label' => 'Posunout :label nahoru',
        ],

        'move_record_down' => [
            'label' => 'Posunout :label dolů',
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

        'toggle_record_content' => [
            'label' => 'Rozbalit/sbalit položku :key',
        ],

    ],

    'empty' => [

        'heading' => 'Žádné záznamy nenalezeny',

        'description' => 'Začněte vytvořením záznamu (:model).',

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

    'loading' => 'Načítá se...',

    'reorder_indicator' => 'Vyberte a přesuňte položky.',

    'reordering' => [
        'position' => ':label, pozice :position z :count.',
        'moved' => ':label je nyní na pozici :position z :count.',
    ],

    'result_count' => '{0} Žádné výsledky|{1} :count výsledek|[2,4] :count výsledky|[5,*] :count výsledků',

    'selection_indicator' => [

        'selected_count' => '{1} 1 záznam zvolen|[2,4] :count záznamy zvoleny|[5,*] :count záznamů zvoleno',

        'actions' => [

            'select_all' => [
                'label' => 'Vybrat vše (:count)',
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

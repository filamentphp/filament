<?php

return [

    'column_manager' => [

        'heading' => 'Stĺpce',

        'actions' => [

            'apply' => [
                'label' => 'Použiť stĺpce',
            ],

            'reset' => [
                'label' => 'Resetovať stĺpce',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Akcia|Akcie',
        ],

        'select' => [

            'loading_message' => 'Načítava sa...',

            'no_search_results_message' => 'Žiadne možnosti nezodpovedajú vášmu vyhľadávaniu.',

            'placeholder' => 'Vyberte možnosť',

            'searching_message' => 'Vyhľadáva sa...',

            'search_prompt' => 'Začnite písať pre vyhľadávanie...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Zobraziť o :count menej',
                'expand_list' => 'Zobraziť o :count viac',
            ],

            'more_list_items' => 'a ďalších :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Označiť/odznačiť všetky položky pre hromadné akcie.',
        ],

        'bulk_select_record' => [
            'label' => 'Označiť/odznačiť položku :key pre hromadné akcie.',
        ],

        'bulk_select_group' => [
            'label' => 'Označiť/odznačiť skupinu :title pre hromadné akcie.',
        ],

        'search' => [
            'label' => 'Hľadať',
            'placeholder' => 'Hľadať',
            'indicator' => 'Hľadať',
        ],

    ],

    'summary' => [

        'heading' => 'Sumár',

        'subheadings' => [
            'all' => 'Všetko',
            'group' => 'Sumár (:group)',
            'page' => 'Táto strana',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Priemer',
            ],

            'count' => [
                'label' => 'Počet',
            ],

            'sum' => [
                'label' => 'Súčet',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Dokončiť zoraďovanie záznamov',
        ],

        'enable_reordering' => [
            'label' => 'Zoradiť záznamy',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Skupina',
        ],

        'open_bulk_actions' => [
            'label' => 'Hromadné akcie',
        ],

        'column_manager' => [
            'label' => 'Prepnúť stĺpce',
        ],

    ],

    'empty' => [

        'heading' => 'Žiadny :model',

        'description' => 'Pre pokračovanie vytvorte :model.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Použiť filtre',
            ],

            'remove' => [
                'label' => 'Odstrániť filter',
            ],

            'remove_all' => [
                'label' => 'Odstrániť všetky filtre',
                'tooltip' => 'Odstrániť všetky filtre',
            ],

            'reset' => [
                'label' => 'Resetovať filtre',
            ],

        ],

        'heading' => 'Filtre',

        'indicator' => 'Aktívne filtre',

        'multi_select' => [
            'placeholder' => 'Všetko',
        ],

        'select' => [

            'placeholder' => 'Všetko',

            'relationship' => [
                'empty_option_label' => 'Žiadna',
            ],

        ],

        'trashed' => [

            'label' => 'Odstránené záznamy',

            'only_trashed' => 'Iba odstránené záznamy',

            'with_trashed' => 'Spolu s ostránenými záznamami',

            'without_trashed' => 'Bez odstránených záznamov',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Zoskupiť podľa',
            ],

            'direction' => [

                'label' => 'Smer zoskupenia',

                'options' => [
                    'asc' => 'Vzostupne',
                    'desc' => 'Zostupne',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Ťahaním presuňte záznamy do požadovaného poradia.',

    'selection_indicator' => [

        'selected_count' => '1 vybraná položka|:count vybraných položiek',

        'actions' => [

            'select_all' => [
                'label' => 'Označiť všetkých :count',
            ],

            'deselect_all' => [
                'label' => 'Zrušiť označenie všetkých',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Zoradiť podľa',
            ],

            'direction' => [

                'label' => 'Smer zoradenia',

                'options' => [
                    'asc' => 'Vzostupne',
                    'desc' => 'Zostupne',
                ],

            ],

        ],

    ],

    'default_model_label' => 'záznam',

];

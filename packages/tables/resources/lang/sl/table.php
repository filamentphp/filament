<?php

return [

    'column_toggle' => [

        'heading' => 'Stolpci',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Pokaži :count manj',
                'expand_list' => 'Pokaži :count več',
            ],

            'more_list_items' => 'in še :count več',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Izberi/odznači vse elemente za skupinska dejanja.',
        ],

        'bulk_select_record' => [
            'label' => 'Izberi/odznači element :key za skupinska dejanja.',
        ],

        'bulk_select_group' => [
            'label' => 'Izberi/odznači skupino :title za skupinska dejanja.',
        ],

        'search' => [
            'label' => 'Išči',
            'placeholder' => 'Išči',
            'indicator' => 'Išči',
        ],

    ],

    'summary' => [

        'heading' => 'Povzetek',

        'subheadings' => [
            'all' => 'Vsi :label',
            'group' => 'Povzetek :group',
            'page' => 'Ta stran',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Povprečje',
            ],

            'count' => [
                'label' => 'Število',
            ],

            'sum' => [
                'label' => 'Vsota',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Zaključi prerazporejanje zapisov',
        ],

        'enable_reordering' => [
            'label' => 'Prerazporedi zapise',
        ],

        'filter' => [
            'label' => 'Filtriraj',
        ],

        'group' => [
            'label' => 'Združi',
        ],

        'open_bulk_actions' => [
            'label' => 'Skupinska dejanja',
        ],

        'toggle_columns' => [
            'label' => 'Prikaži/skrij stolpce',
        ],

    ],

    'empty' => [

        'heading' => 'Ni :model',

        'description' => 'Začnite z ustvarjanjem :model.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Uporabi filtre',
            ],

            'remove' => [
                'label' => 'Odstrani filter',
            ],

            'remove_all' => [
                'label' => 'Odstrani vse filtre',
                'tooltip' => 'Odstrani vse filtre',
            ],

            'reset' => [
                'label' => 'Ponastavi',
            ],

        ],

        'heading' => 'Filtri',

        'indicator' => 'Aktivni filtri',

        'multi_select' => [
            'placeholder' => 'Vsi',
        ],

        'select' => [
            'placeholder' => 'Vsi',
        ],

        'trashed' => [

            'label' => 'Izbrisani zapisi',

            'only_trashed' => 'Samo izbrisani zapisi',

            'with_trashed' => 'Z izbrisanimi zapisi',

            'without_trashed' => 'Brez izbrisanih zapisov',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Združi po',
                'placeholder' => 'Združi po',
            ],

            'direction' => [

                'label' => 'Smer združevanja',

                'options' => [
                    'asc' => 'Naraščajoče',
                    'desc' => 'Padajoče',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Povlecite in spustite zapise, da jih uredite po vrsti.',

    'selection_indicator' => [

        'selected_count' => '1 izbran zapis|2 izbrana zapisa|:count izbranih zapisov',

        'actions' => [

            'select_all' => [
                'label' => 'Izberi vse :count',
            ],

            'deselect_all' => [
                'label' => 'Odznači vse',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Razvrsti po',
            ],

            'direction' => [

                'label' => 'Smer razvrščanja',

                'options' => [
                    'asc' => 'Naraščajoče',
                    'desc' => 'Padajoče',
                ],

            ],

        ],

    ],

];

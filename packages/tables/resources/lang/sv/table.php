<?php

return [

    'column_toggle' => [

        'heading' => 'Kolumner',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'och :count till',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Markera/avmarkera alla rader för massåtgärder.',
        ],

        'bulk_select_record' => [
            'label' => 'Markera/avmarkera rad :key för massåtgärder.',
        ],

        'bulk_select_group' => [
            'label' => 'Markera/avmarkera gruppen :title för massåtgärder.',
        ],

        'search' => [
            'label' => 'Sök',
            'placeholder' => 'Sök',
            'indicator' => 'Sök',
        ],

    ],

    'summary' => [

        'heading' => 'Sammanfattning',

        'subheadings' => [
            'all' => 'Alla :label',
            'group' => ':group sammanfattning',
            'page' => 'Denna sida',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Medelvärde',
            ],

            'count' => [
                'label' => 'Antal',
            ],

            'sum' => [
                'label' => 'Summa',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Sluta ändra ordning på rader',
        ],

        'enable_reordering' => [
            'label' => 'Ändra ordning på rader',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Gruppera',
        ],

        'open_bulk_actions' => [
            'label' => 'Öppna åtgärder',
        ],

        'toggle_columns' => [
            'label' => 'Växla kolumner',
        ],

    ],

    'empty' => [

        'heading' => 'Inga :model',

        'description' => 'Skapa :model för att komma igång.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Ta bort filter',
            ],

            'remove_all' => [
                'label' => 'Ta bort alla filter',
                'tooltip' => 'Ta bort alla filter',
            ],

            'reset' => [
                'label' => 'Återställ',
            ],

        ],

        'heading' => 'Filter',

        'indicator' => 'Aktiva filter',

        'multi_select' => [
            'placeholder' => 'Alla',
        ],

        'select' => [
            'placeholder' => 'Alla',
        ],

        'trashed' => [

            'label' => 'Raderade rader',

            'only_trashed' => 'Endast raderade rader',

            'with_trashed' => 'Med raderade rader',

            'without_trashed' => 'Utan raderade rader',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Gruppera',
                'placeholder' => 'Gruppera efter',
            ],

            'direction' => [

                'label' => 'Riktning',

                'options' => [
                    'asc' => 'Stigande',
                    'desc' => 'Fallande',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Dra och släpp raderna i önskad ordning.',

    'selection_indicator' => [

        'selected_count' => '1 rad vald|:count rader valda',

        'actions' => [

            'select_all' => [
                'label' => 'Markera alla :count',
            ],

            'deselect_all' => [
                'label' => 'Avmarkera alla',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortera efter',
            ],

            'direction' => [

                'label' => 'Sorteringsriktning',

                'options' => [
                    'asc' => 'Stigande',
                    'desc' => 'Fallande',
                ],

            ],

        ],

    ],

];

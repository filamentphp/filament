<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'Ychwanegu :count arall',
        ],

        'messages' => [
            'copied' => 'Wedi Copïo',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Dewis / Dad ddewis pob eitem ar gyfer gweithredoedd swmpus',
        ],

        'bulk_select_record' => [
            'label' => 'Dewis / Dad ddewis eitem :key ar gyfer gweithredoedd swmpus',
        ],

        'search_query' => [
            'label' => 'Chwilio',
            'placeholder' => 'Chwilio',
        ],

    ],

    'pagination' => [

        'label' => 'Gwe-lywio tudalennau',

        'overview' => '{1} Dangos 1 canlyniad|[2,*] Yn dangos :first i :last o :total canlyniadau',

        'fields' => [

            'records_per_page' => [

                'label' => 'fesul tudalen',

                'options' => [
                    'all' => 'Pawb',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Ewch i dudalen :page',
            ],

            'next' => [
                'label' => 'Nesaf',
            ],

            'previous' => [
                'label' => 'Blaenorol',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Gorffen ail archebu cofnodion',
        ],

        'enable_reordering' => [
            'label' => 'Ail archebu cofnodion',
        ],

        'filter' => [
            'label' => 'Hidlo',
        ],

        'open_actions' => [
            'label' => 'Gweithredoedd agored',
        ],

        'toggle_columns' => [
            'label' => 'Toglo colofnau',
        ],

    ],

    'empty' => [

        'heading' => 'Ni ddarganfuwyd unrhyw gofnodion',

        'buttons' => [

            'reset_column_searches' => [
                'label' => 'Clirio colofn chwilio',
            ],

        ],

    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Tynnu hidlydd',
            ],

            'remove_all' => [
                'label' => 'Tynnu pob hidlydd',
                'tooltip' => 'Tynnu pob hidlydd',
            ],

            'reset' => [
                'label' => 'Ailosod hidlyddion',
            ],

        ],

        'indicator' => 'Hidlyddion Gweithredol',

        'multi_select' => [
            'placeholder' => 'Oll',
        ],

        'select' => [
            'placeholder' => 'Oll',
        ],

        'trashed' => [

            'label' => 'Cofnodion wedi dileu',

            'only_trashed' => 'Dim ond cofnodion wedi dileu',

            'with_trashed' => 'Gyda chofnodion wedi dileu',

            'without_trashed' => 'Heb gofnodion wedi dileu',

        ],

    ],

    'reorder_indicator' => 'Llusgo a gollwn y cofnodion mewn trefn',

    'selection_indicator' => [

        'selected_count' => 'Dewiswyd 1 cofnod.|:count rcyfrif wedi`u dewis.',

        'buttons' => [

            'select_all' => [
                'label' => 'Dewiswch bob :count',
            ],

            'deselect_all' => [
                'label' => 'Dad-ddewis popeth',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Trefnu fesul',
            ],

            'direction' => [

                'label' => 'Trefnu cyfeiriad',

                'options' => [
                    'asc' => 'Esgynnol',
                    'desc' => 'Disgynnol',
                ],

            ],

        ],

    ],

];

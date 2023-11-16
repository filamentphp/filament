<?php

return [

    'column_toggle' => [

        'heading' => 'Zutabeak',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'eta :count gehiago',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Aukeratu/deselektatu denak ekintza masiboetarako.',
        ],

        'bulk_select_record' => [
            'label' => 'Aukeratu/deselektatu :key ekintza masiboetarako.',
        ],

        'search' => [
            'label' => 'Bilatu',
            'placeholder' => 'Bilatu',
            'indicator' => 'Bilatu',
        ],

    ],

    'summary' => [

        'heading' => 'Laburpena',

        'subheadings' => [
            'all' => 'Guztiak :label',
            'group' => ':group-aren laburpena',
            'page' => 'Orrialde hau',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Batezbesteko',
            ],

            'count' => [
                'label' => 'Zenbatekoa',
            ],

            'sum' => [
                'label' => 'Baturak',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Erregistroen berrantolaketa amaitu',
        ],

        'enable_reordering' => [
            'label' => 'Erregistroak berrantolatu',
        ],

        'filter' => [
            'label' => 'Iragazkiak',
        ],

        'group' => [
            'label' => 'Taldekatu',
        ],

        'open_bulk_actions' => [
            'label' => 'Ireki ekintzak',
        ],

        'toggle_columns' => [
            'label' => 'Zutabeak aldatu',
        ],

    ],

    'empty' => [

        'heading' => 'Erregistroak ez daude aurkitu',

        'description' => 'Sortu :model bat hasteko.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Iragazkia kendu',
            ],

            'remove_all' => [
                'label' => 'Iragazki guztiak kendu',
                'tooltip' => 'Iragazki guztiak kendu',
            ],

            'reset' => [
                'label' => 'Berrezarri iragazkiak',
            ],

        ],

        'heading' => 'Iragazkiak',

        'indicator' => 'Iragazkiak aktiboak',

        'multi_select' => [
            'placeholder' => 'Guztiak',
        ],

        'select' => [
            'placeholder' => 'Guztiak',
        ],

        'trashed' => [

            'label' => 'Ezabatutako erregistroak',

            'only_trashed' => 'Bakarrik ezabatutako erregistroak',

            'with_trashed' => 'Ezabatutako erregistroekin',

            'without_trashed' => 'Ezabatutako erregistro gabe',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Taldekatu',
                'placeholder' => 'Taldekatu',
            ],

            'direction' => [

                'label' => 'Taldekatzearen norabidea',

                'options' => [
                    'asc' => 'Goranzkoa',
                    'desc' => 'Beheranzkoa',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Erregistroak ordenan eraman ahal izateko arrastatu.',

    'selection_indicator' => [

        'selected_count' => ':count erregistro aukeratu da|:count erregistro aukeratuak dira',

        'actions' => [

            'select_all' => [
                'label' => 'Hautatu guztiak :count',
            ],

            'deselect_all' => [
                'label' => 'Aukeratu guztiak kentzea',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Ordenatu',
            ],

            'direction' => [

                'label' => 'Ordenaren norabidea',

                'options' => [
                    'asc' => 'Goranzkoa',
                    'desc' => 'Beheranzkoa',
                ],

            ],

        ],

    ],

];

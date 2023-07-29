<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'eta :count gehiago',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Ekintza masiboetarako elementu guztiak hautatu/deshautatu.',
        ],

        'bulk_select_record' => [
            'label' => 'Hautatu/deshautatu elementua :keyekintza masiboetarako.',
        ],

        'search_query' => [
            'label' => 'Bilaketa',
            'placeholder' => 'Bilatu',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Erregistroak berrantolatzen amaitu',
        ],

        'enable_reordering' => [
            'label' => 'Erregistroak berrantolatu',
        ],

        'filter' => [
            'label' => 'Filtratu',
        ],

        'open_actions' => [
            'label' => 'Ekintzak ireki',
        ],

        'toggle_columns' => [
            'label' => 'Zutabeak txandakatu',
        ],

    ],

    'empty' => [

        'heading' => 'Ez da erregistrorik aurkitu',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Kendu filtroak',
            ],

            'remove_all' => [
                'label' => 'Kendu filtro guztiak',
                'tooltip' => 'Kendu filtro guztiak',
            ],

            'reset' => [
                'label' => 'Filtroak berrabiarazi',
            ],

        ],

        'indicator' => 'Aktibatutako filtroak',

        'multi_select' => [
            'placeholder' => 'Denak',
        ],

        'select' => [
            'placeholder' => 'Denak',
        ],

        'trashed' => [

            'label' => 'Ezabatutako erregistroak',

            'only_trashed' => 'Ezabatutako erregistroak bakarrik',

            'with_trashed' => 'Ezabatutako erregistroekin',

            'without_trashed' => 'Ezabatutako erregistro gabe',

        ],

    ],

    'reorder_indicator' => 'Arrastatu erregistroak ordenan.',

    'selection_indicator' => [

        'selected_count' => 'Erregistro bat hautatu da|Hautatutako erregistroak: :count',

        'actions' => [

            'select_all' => [
                'label' => 'Hautatu denak :count',
            ],

            'deselect_all' => [
                'label' => 'Deshautatu guztiak',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Honela ordenatu:',
            ],

            'direction' => [

                'label' => 'Ordenaren helbidea',

                'options' => [
                    'asc' => 'Goranzkoa',
                    'desc' => 'Beheranzkoa',
                ],

            ],

        ],

    ],

];

<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'eta :count gehiago',
        ],

        'messages' => [
            'copied' => 'Kopiatuta',
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

    'pagination' => [

        'label' => 'Paginazioaren nabigazioa',

        'overview' => '{1} Emaitza bat erakusten da|[2,*] :total emaitzatik :firstetik :lastera erakusten dira',

        'fields' => [

            'records_per_page' => [

                'label' => 'orriko',

                'options' => [
                    'all' => 'Denak',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Joan :page orrira',
            ],

            'next' => [
                'label' => 'Hurrengoa',
            ],

            'previous' => [
                'label' => 'Aurrekoa',
            ],

        ],

    ],

    'buttons' => [

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

        'buttons' => [

            'reset_column_searches' => [
                'label' => 'Zutabeen bilaketa garbitu',
            ],

        ],

    ],

    'filters' => [

        'buttons' => [

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

        'selected_count' => 'Erregistro bat hautatu da.|Hautatutako erregistroak: :count',

        'buttons' => [

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

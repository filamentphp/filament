<?php

return [

    'label' => 'Esportatu :label',

    'modal' => [

        'heading' => 'Esportatu :label',

        'form' => [

            'columns' => [

                'label' => 'Zutabeak',

                'actions' => [

                    'select_all' => [
                        'label' => 'Aukeratu guztia',
                    ],

                    'deselect_all' => [
                        'label' => 'Deselektatu guztia',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':column gaituta',
                    ],

                    'label' => [
                        'label' => ':column etiketa',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Esportatu',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Esportazioa osatuta',

            'actions' => [

                'download_csv' => [
                    'label' => 'Deskargatu .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Deskargatu .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Esportazioa handiegia da',
            'body' => 'Ezin duzu errenkada bat baino gehiago esportatu aldi berean.|Ezin dituzu :count errenkada baino gehiago esportatu aldi berean.',
        ],

        'no_columns' => [
            'title' => 'Ez da zutaberik hautatu',
            'body' => 'Mesedez, hautatu gutxienez zutabe bat esportatzeko.',
        ],

        'started' => [
            'title' => 'Esportazioa hasita',
            'body' => 'Esportazioa hasi da eta errenkada bat atzeko planoan prozesatuko da. Deskarga-estekarekin jakinarazpen bat jasoko duzu amaitzean.|Esportazioa hasi da eta :count errenkada atzeko planoan prozesatuko dira. Deskarga-estekarekin jakinarazpen bat jasoko duzu amaitzean.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

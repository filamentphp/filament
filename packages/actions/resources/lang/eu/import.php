<?php

return [

    'label' => 'Inportatu :label',

    'modal' => [

        'heading' => 'Inportatu :label',

        'form' => [

            'file' => [

                'label' => 'Fitxategia',

                'placeholder' => 'Igo CSV fitxategia',

                'rules' => [
                    'duplicate_columns' => '{0} Fitxategiak ezin du zutabe-goiburu huts bat baino gehiago izan.|{1,*} Fitxategiak ezin ditu bikoiztutako zutabe-goiburuak izan: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Zutabeak',
                'placeholder' => 'Hautatu zutabe bat',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Deskargatu CSV fitxategi eredua',
            ],

            'import' => [
                'label' => 'Inportatu',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Inportazioa osatuta',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Deskargatu huts egindako errenkadaren informazioa|Deskargatu huts egindako errenkaden informazioa',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Igotako CSV fitxategia handiegia da',
            'body' => 'Ezin duzu errenkada bat baino gehiago inportatu aldi berean.|Ezin dituzu :count errenkada baino gehiago inportatu aldi berean.',
        ],

        'started' => [
            'title' => 'Inportazioa hasita',
            'body' => 'Inportazioa hasi da eta errenkada bat atzeko planoan prozesatuko da.|Inportazioa hasi da eta :count errenkada atzeko planoan prozesatuko dira.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'errorea',
        'system_error' => 'Sistemaren errorea, jarri harremanetan laguntza-zerbitzuarekin.',
        'column_mapping_required_for_new_record' => ':attribute zutabea ez da fitxategiko zutabe batekin mapeatu, baina beharrezkoa da erregistro berriak sortzeko.',
    ],

];

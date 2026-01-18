<?php

return [

    'label' => 'Uvezi :label',

    'modal' => [

        'heading' => 'Uvezi :label',

        'form' => [

            'file' => [

                'label' => 'Datoteka',

                'placeholder' => 'Prilkači CSV datoteka',

                'rules' => [
                    'duplicate_columns' => '{0} Datotekata ne smee da sodrži povekje od edna prazna kolona za naslov.|{1,*} Datotekata ne smee da sodrži duplirani koloni za naslov: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Koloni',
                'placeholder' => 'Izberi kolona',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Prezemi primer CSV datoteka',
            ],

            'import' => [
                'label' => 'Uvezi',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Uvozot e završen',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Prezemi informacii za neuspešniot red|Prezemi informacii za neuspešnite redovi',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Prilkačenata CSV datoteka e pregolema',
            'body' => 'Ne možete da uvezuvate povekje od 1 red odenaš.|Ne možete da uvezuvate povekje od :count redovi odenaš.',
        ],

        'started' => [
            'title' => 'Uvozot e započnat',
            'body' => 'Vašiot uvoz započna i 1 red kje bide obraboten vo pozadina.|Vašiot uvoz započna i :count redovi kje bidat obraboteni vo pozadina.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'greška',
        'system_error' => 'Sistemska greška, ve molime kontaktirajte so poddrška.',
        'column_mapping_required_for_new_record' => 'Kolonata :attribute ne beše mapirana na kolona vo datotekata, no e zadolžitelna za kreiranje na novi zapisi.',
    ],

];

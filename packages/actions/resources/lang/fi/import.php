<?php

return [

    'label' => 'Tuo :label',

    'modal' => [

        'heading' => 'Tuo :label',

        'form' => [

            'file' => [
                'label' => 'Tiedosto',
                'placeholder' => 'Siirrä CSV tiedosto',
            ],

            'columns' => [
                'label' => 'Sarakkeet',
                'placeholder' => 'Valitse sarake',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Lataa esimerkki CSV-tiedosto',
            ],

            'import' => [
                'label' => 'Tuonti',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Tuonti valmis',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Lataa tietoja epäonnistuneesta rivistä|Lataa tietoja epäonnistuneesta riveistä',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Siirretty CSV on liian iso',
            'body' => 'Voi tuoda vain yhden rivin kerrallaan.|Voit tuoda vain :count riviä kerralla.',
        ],

        'started' => [
            'title' => 'Tuonti aloitettu',
            'body' => 'Tuonti on aloitettu ja 1 rivi käsitellään taustalla.|Tuonti on aloitettu ja :count riviä käsitellään taustalla.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'virhe',
        'system_error' => 'Järjestelmävirhe, ota yhteyttä tukeen.',
    ],

];

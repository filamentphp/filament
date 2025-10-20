<?php

return [

    'label' => 'Vie :label',

    'modal' => [

        'heading' => 'Vie :label',

        'form' => [

            'columns' => [

                'label' => 'Sarakkeet',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column valittu',
                    ],

                    'label' => [
                        'label' => ':column nimike',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Vie',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Vienti valmis',

            'actions' => [

                'download_csv' => [
                    'label' => 'Lataa .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Lataa .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Vienti on liian iso',
            'body' => 'Et voi viedä kuin yhden rivin kerralla.|Et voi viedä kuin :count riviä yhdellä kerralla.',
        ],

        'started' => [
            'title' => 'Vienti aloitettu',
            'body' => 'Vienti on aloitettu ja 1 rivi käsitellään taustalla. Saat ilmoituksen latauslinkillä kun se on valmis.|Vienti on aloitettu ja :count riviä käsitellään taustalla. Saat ilmoituksen latauslinkillä kun se on valmis.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

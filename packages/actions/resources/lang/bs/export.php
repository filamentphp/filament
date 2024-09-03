<?php

return [

    'label' => 'Eksportujte :label',

    'modal' => [

        'heading' => 'Exportujte :label',

        'form' => [

            'columns' => [

                'label' => 'Kolone',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column kolona aktivna',
                    ],

                    'label' => [
                        'label' => ':column oznaka',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Eksportujte',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Eksport završen',

            'actions' => [

                'download_csv' => [
                    'label' => 'Preuzmite .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Preuzmite .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Eksport je prevelik.',
            'body' => 'Ne možete eksportovati više od 1 reda odjednom.|Ne možete eksportovati više od :count redova odjednom',
        ],

        'started' => [
            'title' => 'Eksport započet',
            'body' => 'Vaš eksport je započeo i 1 red će se obrađivati u pozadini. Dobit ćete obavijest s linkom za preuzimanje kada bude dovršeno. | Vaš eksport je započeo i :count redova će se obrađivati u pozadini. Dobit ćete obavijest s linkom za preuzimanje kada bude dovršeno.',
        ],

    ],

    'file_name' => 'eksport-:export_id-:model',

];

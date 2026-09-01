<?php

return [
    'label' => 'Izvezi :label',

    'modal' => [

        'heading' => 'Izvezi :label',

        'form' => [

            'columns' => [

                'label' => 'Stupci',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column je omogućen',
                    ],

                    'label' => [
                        'label' => ':column oznaka',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Izvezi',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Izvezi podataka završen',

            'actions' => [

                'download_csv' => [
                    'label' => 'Preuzmi .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Preuzmi .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Datoteka za preuzimanje je prevelika',
            'body' => '{1} Ne možete preuzeti više od jednog reda odjednom.|[2,4] Ne možete preuzeti više od :count reda odjednom.|[5,*] Ne možete preuzeti više od :count redova odjednom.',
        ],

        'started' => [
            'title' => 'Izvoz datoteka je započet',
            'body' => '{1} Tvoj izvoz datoteka je započet i jedan red će se obraditi u pozadini. Dobićete obaveštenje kada je izrađena veza za preuzimanje.|[2,4] Tvoj izvoz datoteka je započeo i :count reda će se obraditi u pozadini. Dobićete obaveštenje kada je izrađena veza za preuzimanje.|[5,*] Tvoj izvoz datoteka je započeo i :count redova će se obraditi u pozadini. Dobićete obaveštenje kada je izrađena veza za preuzimanje.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

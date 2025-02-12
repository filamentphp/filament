<?php

return [

    'label' => 'Voer :label uit',

    'modal' => [

        'heading' => 'Voer :label uit',

        'form' => [

            'columns' => [

                'label' => 'Kolomme',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column geaktiveer',
                    ],

                    'label' => [
                        'label' => ':column etiket',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Uitvoer',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Uitvoer voltooi',

            'actions' => [

                'download_csv' => [
                    'label' => 'Laai .csv af',
                ],

                'download_xlsx' => [
                    'label' => 'Laai .xlsx af',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Uitvoer is te groot',
            'body' => 'Jy mag nie meer as 1 ry op een slag uitvoer nie.|Jy mag nie meer as :count rye op een slag uitvoer nie.',
        ],

        'started' => [
            'title' => 'Uitvoer het begin',
            'body' => 'Jou uitvoer het begin en 1 ry sal in die agtergrond verwerk word. Jy sal \'n kennisgewing met die aflaaiskakel ontvang wanneer dit voltooi is.|Jou uitvoer het begin en :count rye sal in die agtergrond verwerk word. Jy sal \'n kennisgewing met die aflaaiskakel ontvang wanneer dit voltooi is.',
        ],

    ],

    'file_name' => 'uitvoer-:export_id-:model',

];

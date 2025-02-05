<?php

return [

    'label' => 'Ekspordi :label',

    'modal' => [

        'heading' => 'Ekspordi :label',

        'form' => [

            'columns' => [

                'label' => 'Veerud',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column lubatud',
                    ],

                    'label' => [
                        'label' => ':column silt',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Ekspordi',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Eksport lõpetatud',

            'actions' => [

                'download_csv' => [
                    'label' => 'Laadi alla .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Laadi alla .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Eksport on liiga suur',
            'body' => 'Korraga ei saa eksportida rohkem kui 1 rida.|Korraga ei saa eksportida rohkem kui :count rida.',
        ],

        'started' => [
            'title' => 'Eksport alustatud',
            'body' => 'Sinu eksport on alanud ja 1 rida töödeldakse taustal. Kui see on valmis, saad teavituse koos allalaadimislingiga.|Sinu eksport on alanud ja :count rida töödeldakse taustal. Kui see on valmis, saad teavituse koos allalaadimislingiga.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

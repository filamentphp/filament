<?php

return [

    'label' => 'Export :label',

    'modal' => [

        'heading' => 'Export :label',

        'form' => [

            'columns' => [

                'label' => 'Stĺpce',

                'actions' => [

                    'select_all' => [
                        'label' => 'Vybrať všetko',
                    ],

                    'deselect_all' => [
                        'label' => 'Zrušiť výber všetkých',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':column povolené',
                    ],

                    'label' => [
                        'label' => ':column popis',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Exportovať',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Export dokončený',

            'actions' => [

                'download_csv' => [
                    'label' => 'Stiahnuť .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Stiahnuť .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Export je príliš veľký',
            'body' => 'Naraz nemôžete exportovať viac ako 1 riadok.|Naraz nemôžete exportovať viac ako :count riadkov.',
        ],

        'no_columns' => [
            'title' => 'Nie sú vybrané žiadne stĺpce',
            'body' => 'Prosím vyberte aspoň jeden stĺpec na export.',
        ],

        'started' => [
            'title' => 'Export zahájený',
            'body' => 'Export bol zahájený a 1 riadok sa spracuje na pozadí.|Export bol zahájený a :count riadkov sa spracuje na pozadí.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

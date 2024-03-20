<?php

return [

    'label' => ':label importálása',

    'modal' => [

        'heading' => ':label importálása',

        'form' => [

            'file' => [
                'label' => 'Fájl',
                'placeholder' => 'Tölts fel egy CSV fájlt',
            ],

            'columns' => [
                'label' => 'Oszlopok',
                'placeholder' => 'Válassz egy oszlopot',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Minta CSV fájl letöltése',
            ],

            'import' => [
                'label' => 'Importálás',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Importálás befejezve',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Információ letöltése a sikertelenül importált sorról|Információ letöltése a sikertelenül importált sorokról',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'A feltöltött CSV fájl túl nagy',
            'body' => 'Egyszerre nem importálhatsz több mint :count sort.|Egyszerre nem importálhatsz több mint :count sort.',
        ],

        'started' => [
            'title' => 'Importálás elkezdve',
            'body' => 'A fájl importálása elkezdődött és :count sor feldolgozása jelenleg is zajlik a háttérben.|A fájl importálása elkezdődött és :count sor feldolgozása jelenleg is zajlik a háttérben.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-minta',
    ],

    'failure_csv' => [
        'file_name' => 'importálás-:import_id-:csv_name-sikertelen-sorok',
        'error_header' => 'hiba',
        'system_error' => 'Rendszerhiba, kérlek lépj kapcsolatba az ügyfélszolgálattal.',
    ],

];

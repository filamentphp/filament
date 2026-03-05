<?php

return [

    'label' => 'Importuoti :label',

    'modal' => [

        'heading' => 'Importuoti :label',

        'form' => [

            'file' => [
                'label' => 'Failas',

                'placeholder' => 'Įkelti CSV failą',

                'rules' => [
                    'duplicate_columns' => '{0} Failas neturi turėti daugiau nei vieną tuščią stulpelio antraštę.|{1,*} Failas neturi turėti pasikartojančių stulpelio antraščių: :columns.',
                ],
            ],

            'columns' => [
                'label' => 'Stulpeliai',
                'placeholder' => 'Pasirinkite stulpelį',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Atsisiųsti CSV pavyzdį',
            ],

            'import' => [
                'label' => 'Importuoti',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Importas atliktas',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Atsisiųsti informaciją apie nepavykusią eilutę|Atsisiųsti informaciją apie nepavykusias eilutes',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Įkeltas CSV filas per didelis',
            'body' => 'Negalite importuoti daugiau nei 1 eilutės vienu metu.|Negalite importuoti dagiau nei :count eilučių vienu metu.',
        ],

        'started' => [
            'title' => 'Importas pradėtas',
            'body' => 'Importas pradėtas ir 1 eilutė bus apdorojama fone.|Importas pradėtas ir :count eilutės bus apdorojamos fone.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'klaida',
        'system_error' => 'Sistemos klaida, susisiekite su klientų aptarnavimu.',
        'column_mapping_required_for_new_record' => 'Stulpelis :attribute nebuvo susietas su failo stulpeliu, tačiau jis yra būtinas naujiems įrašams kurti.',
    ],

];

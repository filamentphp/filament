<?php

return [
    'label' => 'Uvezi :label',

    'modal' => [

        'heading' => 'Uvezi :label',

        'form' => [

            'file' => [

                'label' => 'Datoteka',

                'placeholder' => 'Izaberi CSV datoteku',
                'rules' => [
                    'duplicate_columns' => '{0} Datoteka ne sme da sadrži više od jedne prazne zaglavlje kolone.|{1,*} Datoteka ne sme da sadrži duplirana zaglavlja kolona: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Kolona',
                'placeholder' => 'Izaberi kolonu',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Preuzmi primer CSV datoteke',
            ],

            'import' => [
                'label' => 'Uvoz',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Uvoz je završen',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Preuzmi informaciju o neuspešnom pokušaju preuzimanja reda|Preuzmi informaciju o neuspešnom pokušaju preuzimanja redova',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Poslata CSV datoteka je prevelika',
            'body' => '{1} Ne možeš uvesti više od jednog reda odjednom.|[2,4] Ne možeš uvesti više od :count reda odjednom.|[5,*] Ne možeš uvesti više od :count redova odjednom.',
        ],

        'started' => [
            'title' => 'Uvoz je započet',
            'body' => '{1} Uvoz je započet i jedan red će se obraditi u pozadini.|[2,4] Uvoz je započet i :count reda će se obraditi u pozadini.|[5,*] Uvoz je započet i :count redova će se obraditi u pozadini.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'greška',
        'system_error' => 'Sistemska greška, kontaktirajte podršku.',
        'column_mapping_required_for_new_record' => ':attribute kolona nije mapirana na kolonu u datoteci, ali je obavezna za kreiranje novih zapisa.',
    ],

];

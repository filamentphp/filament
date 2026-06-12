<?php

return [

    'label' => 'Impordi :label',

    'modal' => [

        'heading' => 'Impordi :label',

        'form' => [

            'file' => [

                'label' => 'Fail',

                'placeholder' => 'Laadi üles CSV fail',

                'rules' => [
                    'duplicate_columns' => '{0} Fail ei tohi sisaldada rohkem kui ühte tühja veeru pealkirja.|{1,*} Fail ei tohi sisaldada ühesuguseid veeru pealkirju: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Veerud',
                'placeholder' => 'Vali veerg',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Laadi alla näidis CSV fail',
            ],

            'import' => [
                'label' => 'Impordi',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Imporditud',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Laadi alla teave vigase rea kohta|Laadi alla teave vigaste ridade kohta',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Üles laaditud CSV fail on liiga suur',
            'body' => 'Ei saa korraga importida rohkem kui 1 rida.|Ei saa korraga importida rohkem kui :count rida.',
        ],

        'started' => [
            'title' => 'Impordimine alustatud',
            'body' => 'Import on alanud ja 1 rida töödeldakse taustal.|Import on alanud ja :count rida töödeldakse taustal.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':import-naidis',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-vigased-read',
        'error_header' => 'viga',
        'system_error' => 'Süsteemiviga, võta ühendust toega.',
        'column_mapping_required_for_new_record' => 'Veerg :attribute ei olnud kirjeldatud failis, kuid see on vajalik uute kirje loomisel.',
    ],

];

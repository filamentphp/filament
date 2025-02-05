<?php

return [

    'label' => 'Impordi :label',

    'modal' => [

        'heading' => 'Impordi :label',

        'form' => [

            'file' => [

                'label' => 'Fail',

                'placeholder' => 'Lae üles CSV fail',

                'rules' => [
                    'duplicate_columns' => '{0} Fail ei tohi sisaldada rohkem kui ühte tühja veeru päist.|{1,*} Fail ei tohi sisaldada korduvaid veeru päiseid: :columns.',
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

            'title' => 'Import lõpetatud',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Laadi alla info ebaõnnestunud rea kohta|Laadi alla info ebaõnnestunud ridade kohta',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Üleslaetud CSV fail on liiga suur',
            'body' => 'Korraga ei saa importida rohkem kui 1 rida.|Korraga ei saa importida rohkem kui :count rida.',
        ],

        'started' => [
            'title' => 'Import alustatud',
            'body' => 'Sinu import on alanud ja 1 rida töödeldakse taustal.|Sinu import on alanud ja :count rida töödeldakse taustal.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'Süsteemi viga, palun võtke ühendust toega.',
        'column_mapping_required_for_new_record' => 'Veerg :attribute ei ole seotud ühegi veeruga failis, kuid see on vajalik uute kirjete loomiseks.',
    ],

];

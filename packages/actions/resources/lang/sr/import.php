<?php

return [
    'label' => 'Увези :label',

    'modal' => [

        'heading' => 'Увези :label',

        'form' => [

            'file' => [

                'label' => 'File',

                'placeholder' => 'Пренеси CSV фајл',
                'rules' => [
                    'duplicate_columns' => '{0} |Фајл не сме да садржи више од једног празног заглавља колоне.{1,*} Фајл не сме да садржи дупликате заглавља колона: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Стубац',
                'placeholder' => 'Изабери стубац',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Преузмир пример фајла',
            ],

            'import' => [
                'label' => 'Увоз',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Увоз података је завршен',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Преузми инфо о неуспелом покушају преузимања реда|Преузми инфо о неуспелом покушају преузимања редова',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'CSV фајл је превелик',
            'body' => '{1} Не можете увести више од једног реда одједном.|[2,4] Не можете увести више од :count реда одједном.|[5,*] Не можете увести више од :count редова одједном.',
        ],

        'started' => [
            'title' => 'Увоз је отпочео',
            'body' => '{1} Увоз је започео и један ред ће се обрадити у позадини.|[2,4] Увоз је започео и :count реда ће се обрадити у позадини.|[5,*] Увоз је започео и :count редова ће се обрадити у позадини.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'System error, please contact support.',
        'column_mapping_required_for_new_record' => 'The :attribute column was not mapped to a column in the file, but it is required for creating new records.',
    ],

];

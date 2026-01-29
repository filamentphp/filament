<?php

return [

    'label' => 'Увези :label',

    'modal' => [

        'heading' => 'Увези :label',

        'form' => [

            'file' => [

                'label' => 'Датотека',

                'placeholder' => 'Прикачи CSV датотека',

                'rules' => [
                    'duplicate_columns' => '{0} Датотеката не смее да содржи повеќе од една празна колона за наслов.|{1,*} Датотеката не смее да содржи дуплирани колони за наслов: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Колони',
                'placeholder' => 'Избери колона',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Преземи пример CSV датотека',
            ],

            'import' => [
                'label' => 'Увези',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Увозот е завршен',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Преземи информации за неуспешниот ред|Преземи информации за неуспешните редови',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Прикачената CSV датотека е преголема',
            'body' => 'Не можете да увезувате повеќе од 1 ред одеднаш.|Не можете да увезувате повеќе од :count редови одеднаш.',
        ],

        'started' => [
            'title' => 'Увозот е започнат',
            'body' => 'Вашиот увоз започна и 1 ред ќе биде обработен во позадина.|Вашиот увоз започна и :count редови ќе бидат обработени во позадина.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'грешка',
        'system_error' => 'Системска грешка, ве молиме контактирајте со поддршка.',
        'column_mapping_required_for_new_record' => 'Колоната :attribute не беше мапирана на колона во датотеката, но е задолжителна за креирање на нови записи.',
    ],

];

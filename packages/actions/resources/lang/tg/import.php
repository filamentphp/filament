<?php

return [

    'label' => 'Воридоти :label',

    'modal' => [

        'heading' => 'Воридоти :label',

        'form' => [

            'file' => [

                'label' => 'Файл',

                'placeholder' => 'Боргузории файли CSV',

                'rules' => [
                    'duplicate_columns' => '{0} Файл набояд зиёда аз як сарлавҳаи холии сутун дошта бошад.|{1,*} Файл набояд сарлавҳаҳои такрории сутун дошта бошад: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Сутунҳо',
                'placeholder' => 'Сутунро интихоб кунед',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Боргирии намунаи файли CSV',
            ],

            'import' => [
                'label' => 'Воридот',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Воридот анҷом ёфт',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Боргирии маълумоти сатрҳои ноком',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Файли CSV-и боршуда хеле калон аст.',
            'body' => 'Шумо наметавонед дар як вақт зиёда аз 1 сатрро ворид кунед.|Шумо наметавонед дар як вақт зиёда аз :count сатрро ворид кунед.',
        ],

        'started' => [
            'title' => 'Воридот оғоз шуд',
            'body' => 'Воридот оғоз шуд ва 1 сатр дар замина коркард мешавад.|Воридот оғоз шуд ва :count сатр дар замина коркард мешавад.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'Хато',
        'system_error' => 'Хатои низомӣ. Лутфан ба хадамоти дастгирӣ муроҷиат кунед.',
        'column_mapping_required_for_new_record' => 'Барои :attribute бояд мутобиқати сутун дар файл муайян карда шавад, зеро он ҳангоми эҷоди сабтҳои нав ҳатмист.',
    ],

];

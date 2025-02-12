<?php

return [

    'label' => 'Invoer :label',

    'modal' => [

        'heading' => 'Invoer :label',

        'form' => [

            'file' => [

                'label' => 'Lêer',

                'placeholder' => 'Laai \'n CSV-lêer op',

                'rules' => [
                    'duplicate_columns' => '{0} Die lêer mag nie meer as een leë kolomopskrif bevat nie.|{1,*} Die lêer moet nie duplikaatkolomopskrifte: :columns bevat nie.',
                ],

            ],

            'columns' => [
                'label' => 'Kolomme',
                'placeholder' => 'Kies \'n kolom\'',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Laai voorbeeld CSV-lêer af',
            ],

            'import' => [
                'label' => 'Invoer',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Invoer voltooi',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Laai inligting oor die mislukte ry af|Laai inligting oor die mislukte rye af',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Opgelaaide CSV-lêer is te groot',
            'body' => 'Jy mag nie meer as 1 ry op een slag invoer nie.|Jy mag nie meer as :count rye op een slag invoer nie.',
        ],

        'started' => [
            'title' => 'Invoer het begin',
            'body' => 'Jou invoer het begin en 1 ry sal in die agtergrond verwerk word.|Jou invoer het begin en :count rye sal in die agtergrond verwerk word.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'invoer-:import_id-:csv_name-failed-rows',
        'error_header' => 'fout',
        'system_error' => 'Stelselfout, kontak asseblief ondersteuning.',
        'column_mapping_required_for_new_record' => 'Die :attribute kolom is nie na \'n kolom in die lêer gekarteer nie, maar dit word vereis vir die skep van nuwe rekords.',
    ],

];

<?php

return [

    'label' => 'Uvozi :label',

    'modal' => [

        'heading' => 'Uvozi :label',

        'form' => [

            'file' => [

                'label' => 'Datoteka',

                'placeholder' => 'Naloži CSV datoteko',

                'rules' => [
                    'duplicate_columns' => '{0} Datoteka ne sme vsebovati več kot enega praznega imena stolpca.|{1,*} Datoteka ne sme vsebovati podvojenih imen stolpcev: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Stolpci',
                'placeholder' => 'Izberi stolpec',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Prenesi primer CSV datoteke',
            ],

            'import' => [
                'label' => 'Uvozi',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Uvoz zaključen',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Prenesi informacije o neuspešni vrstici|Prenesi informacije o neuspešnih vrsticah',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Naložena CSV datoteka je prevelika',
            'body' => 'Naenkrat lahko uvozite največ 1 vrstico.|Naenkrat lahko uvozite največ :count vrstic.',
        ],

        'started' => [
            'title' => 'Uvoz se je začel',
            'body' => 'Vaš uvoz se je začel in 1 vrstica bo obdelana v ozadju.|Vaš uvoz se je začel in :count vrstic bo obdelanih v ozadju.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'uvoz-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'Sistemska napaka, prosimo kontaktirajte podporo.',
        'column_mapping_required_for_new_record' => 'Stolpec :attribute ni bil povezan s stolpcem v datoteki, vendar je obvezen za ustvarjanje novih zapisov.',
    ],

];

<?php

return [

    'label' => 'Importujte :label',

    'modal' => [

        'heading' => 'Importujte :label',

        'form' => [

            'file' => [

                'label' => 'Fajl',

                'placeholder' => 'Priložite CSV fajl',

                'rules' => [
                    'duplicate_columns' => '{0} Fajl ne smije sadržavati više od jednog praznog zaglavlja kolone.|{1,*} Fajl ne smije sadržavati duplicirana zaglavlja kolona: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Kolone',
                'placeholder' => 'Odaberite kolonu',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Preuzmite primjer CSV fajla',
            ],

            'import' => [
                'label' => 'Importujte',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import završen',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Preuzmite informacije o neuspješnom redu|Preuzmite informacije o neuspješnim redovima',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Učitani CSV fajl je prevelik',
            'body' => 'Ne možete importovati više od 1 reda odjednom.|Ne možete uvesti više od :count redova odjednom.',
        ],

        'started' => [
            'title' => 'Import započet',
            'body' => 'Vaš uvoz je započeo i 1 red će se obrađivati u pozadini.|Vaš uvoz je započeo i :count redova će se obrađivati u pozadini.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-primjer',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-neuspješni-redovi',
        'error_header' => 'greška',
        'system_error' => 'Sistemska greška, molimo kontaktirajte podršku.',
        'column_mapping_required_for_new_record' => ':attribute kolona nije povezana s kolonom u fajlu, a potrebna je za kreiranje novih zapisa.',
    ],

];

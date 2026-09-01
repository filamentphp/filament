<?php

return [

    'label' => 'Importă :label',

    'modal' => [

        'heading' => 'Importă :label',

        'form' => [

            'file' => [

                'label' => 'Fișier',

                'placeholder' => 'Încarcă un fișier CSV',

                'rules' => [
                    'duplicate_columns' => '{0} Fișierul nu poate conține mai mult de un antet de coloană gol.|{1,*} Fișierul nu poate conține antete de coloane duplicate: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Coloane',
                'placeholder' => 'Alege Coloanele',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Descarcă exemplu în format CSV',
            ],

            'import' => [
                'label' => 'Importă',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import finalizat',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Descarcă informațiiler despre câmpul ce a avut eroaro|Descarcă informații despre câmpurile ce au auvt erori',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Fișierul CSV file este prea mare',
            'body' => 'Nu este permis importul a mai mult de o linile într-un singur fișier.|Nu este permis imporutl a mai mult de :count linii într-un singur fișier.',
        ],

        'started' => [
            'title' => 'Importul a început',
            'body' => 'Importul dumneavostră a început, iar rândul va fi procesa în fundal.|Importul dumneavoastră a început, iar cele :count rânduri vor fi procesate în fundal.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-randuri-esuate',
        'error_header' => 'eroare',
        'system_error' => 'Eroare de sistem, vă rugăm contactați echipa de suport.',
        'column_mapping_required_for_new_record' => 'Coloana :attribute nu a fost mapată la o coloană din fișier, dar este necesară pentru crearea înregistrărilor noi.',
    ],

];

<?php

return [

    'label' => 'Ingiza :label',

    'modal' => [
        'heading' => 'Ingiza :label',

        'form' => [
            'file' => [
                'label' => 'Faili',

                'placeholder' => 'Pakia faili la CSV',

                'rules' => [
                    'duplicate_columns' => '{0} Faili hairuhusiwi kuwa na zaidi ya kichwa kimoja tupu cha safu wima.|{1,*} Faili hairuhusiwi kuwa na vichwa vinavyojirudia vya safu wima: :columns.',
                ],
            ],

            'columns' => [
                'label' => 'Safu wima',
                'placeholder' => 'Chagua safu wima',
            ],
        ],

        'actions' => [
            'download_example' => [
                'label' => 'Pakua faili mfano la CSV',
            ],

            'import' => [
                'label' => 'Ingiza',
            ],
        ],
    ],

    'notifications' => [
        'completed' => [
            'title' => 'Kuingiza Kumekamilika',

            'actions' => [
                'download_failed_rows_csv' => [
                    'label' => 'Pakua maelezo ya mstari ulioshindikana|Pakua maelezo ya mistari iliyoshindikana',
                ],
            ],
        ],

        'max_rows' => [
            'title' => 'Faili la CSV lililopakiwa ni kubwa mno',
            'body' => 'Huwezi kuingiza zaidi ya mstari 1 kwa wakati mmoja.|Huwezi kuingiza zaidi ya mistari :count kwa wakati mmoja.',
        ],

        'started' => [
            'title' => 'Kuingiza Kumeanza',
            'body' => 'Kuingiza kwako kumeanza na mstari 1 utashughulikiwa nyuma ya pazia.|Kuingiza kwako kumeanza na mistari :count itashughulikiwa nyuma ya pazia.',
        ],
    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'hitilafu',
        'system_error' => 'Hitilafu ya mfumo, tafadhali wasiliana na usaidizi.',
        'column_mapping_required_for_new_record' => 'Safu wima ya :attribute haijahusishwa na safu wima yoyote katika faili, lakini inahitajika kuunda rekodi mpya.',
    ],

];

<?php

return [

    'label' => ':labelን ኢምፖርት',

    'modal' => [

        'heading' => ':labelን ኢምፖርት',

        'form' => [

            'file' => [
                'label' => 'ፋይል',
                'placeholder' => 'የCSV ፋይል ይስቀሉ።',
            ],

            'columns' => [
                'label' => 'አምዶች',
                'placeholder' => 'አምድ ይምረጡ',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'ምሳሌ የCSV ፋይል ያውርዱ',
            ],

            'import' => [
                'label' => 'ኢምፖርት',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'ኢምፖርቱ ተጠናቆዋል',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'ስለ ያልተሳካው ረድፍ መረጃ ያውርዱ|ስለ ያልተሳኩ ረድፎች መረጃ ያውርዱ',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'የተጫነው የCSV ፋይል በጣም ትልቅ ነው።',
            'body' => 'በአንድ ጊዜ ከ1 ረድፍ በላይ መጫን አይችሉም።|ረድፎችን በአንድ ጊዜ ከ:count በላይ ማስገባት አይችሉም።',
        ],

        'started' => [
            'title' => 'ኢምፖርቱ ተጀምሮዋል',
            'body' => 'መጫን ጀምሯል እና 1 ረድፍ ከበስተጀርባ ይካሄዳል።|የእርስዎ መጫን ጀምሯል እና :count ረድፎች ከበስተጀርባ ይሰራሉ።',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'ስህተት',
        'system_error' => 'የሲስተም ስህተት፣ እባክዎ ድጋፍን ያግኙ።',
    ],

];

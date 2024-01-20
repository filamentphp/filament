<?php

return [

    'label' => 'ដាក់ចូល :label',

    'modal' => [

        'heading' => 'ដាក់ចូល :label',

        'form' => [

            'file' => [
                'label' => 'ឯកសារ',
                'placeholder' => 'ផ្ទុកឡើងឯកសារ CSV',
            ],

            'columns' => [
                'label' => 'ជួរឈរ',
                'placeholder' => 'ជ្រើសរើសជួរឈរ',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'ទាញយកឯកសារ CSV (ឧទាហរណ៍)',
            ],

            'import' => [
                'label' => 'ដាក់ចូល',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'ដាក់ចូលបានបញ្ចប់',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'ទាញយកព័ត៌មានអំពីជួរដែលបរាជ័យ|ទាញយកព័ត៌មានអំពីជួរដែលបរាជ័យ',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'ឯកសារ CSV ដែលបានបង្ហោះមានទំហំធំពេក',
            'body' => 'អ្នកមិនអាចនាំចូលលើសពី 1 ជួរក្នុងពេលតែមួយបានទេ។|អ្នកមិនអាចនាំចូលលើសពី :count ជួរដេកក្នុងពេលតែមួយ។',
        ],

        'started' => [
            'title' => 'ចាប់ផ្ដើមដាក់ចូល',
            'body' => 'ការនាំចូលរបស់អ្នកបានចាប់ផ្តើម ហើយ 1 ជួរនឹងត្រូវបានដំណើរការនៅផ្ទៃខាងក្រោយ។|ការនាំចូលរបស់អ្នកបានចាប់ផ្តើមហើយ :count ជួរដេកនឹងត្រូវបានដំណើរការនៅផ្ទៃខាងក្រោយ។',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'កំហុស',
        'system_error' => 'ប្រព័ន្ធមានកំហុស,សូមទាក់ទងផ្នែកជំនួយ។',
    ],

];

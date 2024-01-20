<?php

return [

    'label' => 'ទាញយក :label',

    'modal' => [

        'heading' => 'ទាញយក :label',

        'form' => [

            'columns' => [

                'label' => 'ជួរឈរ',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column បើកដំណើរការ',
                    ],

                    'label' => [
                        'label' => ':column ស្លាក សញ្ញា',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'ទាញយក',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'បញ្ចប់ការទាញយក',

            'actions' => [

                'download_csv' => [
                    'label' => 'ទាញយក .csv',
                ],

                'download_xlsx' => [
                    'label' => 'ទាញយក .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'ការទាញយកមានទំហំធំពេក',
            'body' => 'អ្នកមិនអាចទាញយកលើសពី 1 ជួរក្នុងពេលតែមួយបានទេ។ | អ្នកមិនអាចទាញយកលើសពី :count ជួរក្នុងពេលតែមួយបានទេ។',
        ],

        'started' => [
            'title' => 'ចាប់ផ្ដើមទាញយក',
            'body' => 'ការទាញយករបស់អ្នកបានចាប់ផ្តើមហើយ 1 ជួរនឹងត្រូវបានដំណើរការក្នុងផ្ទៃខាងក្រោយ។ | ការទាញយករបស់អ្នកបានចាប់ផ្តើមហើយ :count ជួរនឹងត្រូវបានដំណើរការក្នុងផ្ទៃខាងក្រោយ។',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

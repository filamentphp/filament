<?php

return [

    'label' => 'នាំចូល :label',

    'modal' => [

        'heading' => 'នាំចូល :label',

        'form' => [

            'file' => [

                'label' => 'ឯកសារ',

                'placeholder' => 'ផ្ទុកឡើងឯកសារ CSV',

                'rules' => [
                    'duplicate_columns' => '{0} ឯកសារមិនត្រូវមានបឋមកថាជួរឈរទទេច្រើនជាងមួយទេ។|{1,*} ឯកសារមិនត្រូវមានបឋមកថាជួរឈរស្ទួនទេ៖ :columns។',
                ],

            ],

            'columns' => [
                'label' => 'ជួរឈរ',
                'placeholder' => 'ជ្រើសរើសជួរឈរ',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'ទាញយកឯកសារ CSV គំរូ',
            ],

            'import' => [
                'label' => 'នាំចូល',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'ការនាំចូលបានបញ្ចប់',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'ទាញយកព័ត៌មានអំពីជួរដែលបរាជ័យ|ទាញយកព័ត៌មានអំពីជួរដែលបរាជ័យ',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'ឯកសារ CSV ដែលបានផ្ទុកឡើងមានទំហំធំពេក',
            'body' => 'អ្នកមិនអាចនាំចូលលើសពី 1 ជួរក្នុងពេលតែមួយបានទេ។|អ្នកមិនអាចនាំចូលលើសពី :count ជួរក្នុងពេលតែមួយបានទេ។',
        ],

        'started' => [
            'title' => 'ការនាំចូលបានចាប់ផ្តើម',
            'body' => 'ការនាំចូលរបស់អ្នកបានចាប់ផ្តើមហើយ 1 ជួរនឹងត្រូវបានដំណើរការនៅផ្ទៃខាងក្រោយ។|ការនាំចូលរបស់អ្នកបានចាប់ផ្តើមហើយ :count ជួរនឹងត្រូវបានដំណើរការនៅផ្ទៃខាងក្រោយ។',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'កំហុស',
        'system_error' => 'ប្រព័ន្ធមានកំហុស សូមទាក់ទងផ្នែកជំនួយ។',
        'column_mapping_required_for_new_record' => 'ជួរឈរ :attribute មិនត្រូវបានផ្គូផ្គងទៅនឹងជួរឈរក្នុងឯកសារនោះទេ ប៉ុន្តែវាត្រូវបានទាមទារសម្រាប់ការបង្កើតទិន្នន័យថ្មី។',
    ],

];

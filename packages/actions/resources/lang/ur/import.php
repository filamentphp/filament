<?php

return [

    'label' => ':label امپورٹ کریں',

    'modal' => [

        'heading' => ':label امپورٹ کریں',

        'form' => [

            'file' => [

                'label' => 'فائل',

                'placeholder' => 'CSV فائل اپلوڈ کریں',

                'rules' => [
                    'duplicate_columns' => '{0} فائل میں ایک سے زیادہ خالی کالم ہیڈر نہیں ہونے چاہئیں۔|{1,*} فائل میں ایک جیسے کالم ہیڈر نہیں ہونے چاہئیں: :columns۔',
                ],

            ],

            'columns' => [
                'label' => 'کالمز',
                'placeholder' => 'ایک کالم منتخب کریں',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'مثالی CSV فائل ڈاؤن لوڈ کریں',
            ],

            'import' => [
                'label' => 'امپورٹ کریں',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'امپورٹ مکمل ہوگئی',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'ناکام قطار کی معلومات ڈاؤن لوڈ کریں|ناکام قطاروں کی معلومات ڈاؤن لوڈ کریں',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'اپلوڈ کی گئی CSV فائل بہت بڑی ہے',
            'body' => 'آپ ایک وقت میں صرف 1 قطار امپورٹ کر سکتے ہیں۔|آپ ایک وقت میں زیادہ سے زیادہ :count قطاریں امپورٹ کر سکتے ہیں۔',
        ],

        'started' => [
            'title' => 'امپورٹ شروع ہوگئی ہے',
            'body' => 'آپ کی امپورٹ شروع ہوچکی ہے، اور 1 قطار بیک گراؤنڈ میں پروسیس ہوگی۔|آپ کی امپورٹ شروع ہوچکی ہے، اور :count قطاریں بیک گراؤنڈ میں پروسیس ہوں گی۔',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'غلطی',
        'system_error' => 'سسٹم کی خرابی، براہ کرم سپورٹ سے رابطہ کریں۔',
        'column_mapping_required_for_new_record' => ':attribute کالم فائل کے کسی کالم سے میپ نہیں کیا گیا، حالانکہ یہ نیا ریکارڈ بنانے کے لیے ضروری ہے۔',
    ],

];

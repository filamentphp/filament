<?php

return [

    'label' => 'ایمپورت :label',

    'modal' => [

        'heading' => 'ایمپورت :label',

        'form' => [

            'file' => [
                'label' => 'پرونده',
                'placeholder' => 'آپلود یک فایل CSV',
            ],

            'columns' => [
                'label' => 'ستون ها',
                'placeholder' => 'یک ستون را انتخاب کنید',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'دانلود نمونه فایل CSV',
            ],

            'import' => [
                'label' => 'ایمپورت',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'ایمپورت تکمیل شد',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'دانلود اطلاعات در مورد ردیف ناموفق|دانلود اطلاعات در مورد ردیف های ناموفق',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'فایل CSV آپلود شده خیلی بزرگ است',
            'body' => 'شما نمی توانید بیش از 1 ردیف را به طور همزمان وارد کنید.|شما نمی توانید بیش از :count ردیف ها را در یک زمان وارد کنید.',
        ],

        'started' => [
            'title' => 'ایمپورت آغاز شد',
            'body' => 'ایمپورت شما شروع شده است و 1 ردیف در پس‌زمینه پردازش می‌شود.|ایمپورت شما آغاز شده است و ردیف‌های :count در پس‌زمینه پردازش می‌شوند.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'خطا',
        'system_error' => 'خطای سیستمی, لطفا با پشتیبانی تماس بگیرید.',
    ],

];

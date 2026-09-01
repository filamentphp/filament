<?php

return [

    'label' => 'ایمپورت :label',

    'modal' => [

        'heading' => 'ایمپورت :label',

        'form' => [

            'file' => [

                'label' => 'پرونده',

                'placeholder' => 'آپلود یک فایل CSV',

                'rules' => [
                    'duplicate_columns' => '{0} فایل نباید بیش از یک ستون با هدر خالی داشته باشد.|{1,*} فایل نباید شامل ستون‌های با هدر تکراری باشد: :columns.',
                ],

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
            'body' => 'شما نمی توانید بیش از 1 ردیف را به طور همزمان وارد کنید.|شما نمی توانید بیش از :count ردیف را در یک زمان وارد کنید.',
        ],

        'started' => [
            'title' => 'ایمپورت آغاز شد',
            'body' => 'ایمپورت شما شروع شده است و 1 ردیف در پس‌زمینه پردازش می‌شود.|ایمپورت شما آغاز شده است و :count ردیف در پس‌زمینه پردازش می‌شوند.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'خطا',
        'system_error' => 'خطای سیستمی، لطفا با پشتیبانی تماس بگیرید.',
        'column_mapping_required_for_new_record' => 'ستون :attribute به هیچ ستونی در فایل نگاشت نشد، ولی وجود آن برای ایجاد رکوردهای جدید لازم است.',
    ],

];

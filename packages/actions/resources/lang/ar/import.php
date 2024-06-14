<?php

return [

    'label' => 'استيراد :label',

    'modal' => [

        'heading' => 'استيراد :label',

        'form' => [

            'file' => [
                'label' => 'ملف',
                'placeholder' => 'تحميل ملف CSV',
                'rules' => [
                    'duplicate_columns' => '{0} يجب ألا يحتوي الملف على أكثر من عنوان عمود فارغ واحد.|{1,*} يجب ألا يحتوي الملف على عناوين أعمدة مكررة: :columns.',
                ],
            ],

            'columns' => [
                'label' => 'الأعمدة',
                'placeholder' => 'اختر عمود',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'تنزيل مثال لملف CSV',
            ],

            'import' => [
                'label' => 'استيراد',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'اكتمل الاستيراد',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'تنزيل معلومات حول الصف الفاشل|تنزيل معلومات حول الصفوف الفاشلة',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'ملف CSV الذي تم تحميله كبير جدًا',
            'body' => 'لا تستطيع استيراد أكثر من صف واحد في كل مرة.|لا تستطيع استيراد أكثر من :count صف في كل مرة.',
        ],

        'started' => [
            'title' => 'بدء الاستيراد',
            'body' => 'بدأت عملية الاستيراد الخاصة بك وستتم معالجة صف واحد في الخلفية.|بدأت عملية الاستيراد الخاصة بك وستتم معالجة :count صفوف في الخلفية.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'خطأ',
        'system_error' => 'خطأ في النظام، يرجى الاتصال بالدعم.',
    ],

];

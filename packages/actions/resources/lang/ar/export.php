<?php

return [

    'label' => 'تصدير :label',

    'modal' => [

        'heading' => 'تصدير :label',

        'form' => [

            'columns' => [

                'label' => 'الأعمدة',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column مفعل',
                    ],

                    'label' => [
                        'label' => ':column عنوان',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'تصدير',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'أكتمل التصدير',

            'actions' => [

                'download_csv' => [
                    'label' => 'تحميل بصيغة .csv',
                ],

                'download_xlsx' => [
                    'label' => 'تحميل بصيغة .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'الملف الذي تم تحميله كبير جدًا',
            'body' => 'لا يمكنك تصدير أكثر من صف واحد في كل مرة.|لا يمكنك تصدير أكثر من :count صف في كل مرة.',
        ],

        'started' => [
            'title' => 'بدء التصدير',
            'body' => 'بدأت عملية التصدير الخاصة بك وستتم معالجة صف واحد في الخلفية.|بدأت عملية التصدير الخاصة بك وستتم معالجة :count صفوف في الخلفية.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

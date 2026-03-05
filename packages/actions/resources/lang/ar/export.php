<?php

return [

    'label' => 'تصدير :label',

    'modal' => [

        'heading' => 'تصدير :label',

        'form' => [

            'columns' => [

                'label' => 'الأعمدة',

                'actions' => [

                    'select_all' => [
                        'label' => 'تحديد الكل',
                    ],

                    'deselect_all' => [
                        'label' => 'إلغاء تحديد الكل',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':column مفعل',
                    ],

                    'label' => [
                        'label' => 'تسمية :column',
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

            'title' => 'اكتمل التصدير',

            'actions' => [

                'download_csv' => [
                    'label' => 'تنزيل .csv',
                ],

                'download_xlsx' => [
                    'label' => 'تنزيل .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'التصدير كبير جداً',
            'body' => 'لا يمكنك تصدير أكثر من صف واحد في المرة الواحدة.|لا يمكنك تصدير أكثر من :count صف في المرة الواحدة.',
        ],

        'no_columns' => [
            'title' => 'لم يتم تحديد أعمدة',
            'body' => 'يرجى تحديد عمود واحد على الأقل للتصدير.',
        ],

        'started' => [
            'title' => 'بدأ التصدير',
            'body' => 'بدأ التصدير الخاص بك وسيتم معالجة صف واحد في الخلفية. ستتلقى إشعاراً برابط التنزيل عند الانتهاء.|بدأ التصدير الخاص بك وسيتم معالجة :count صف في الخلفية. ستتلقى إشعاراً برابط التنزيل عند الانتهاء.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

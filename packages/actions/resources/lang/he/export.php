<?php

return [

    'label' => 'ייצוא :label',

    'modal' => [

        'heading' => 'ייצוא :label',

        'form' => [

            'columns' => [

                'label' => 'עמודות',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column מופעל',
                    ],

                    'label' => [
                        'label' => ':column שם',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'ייצא',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'ייצוא הושלם',

            'actions' => [

                'download_csv' => [
                    'label' => 'הורדת קובץ .csv',
                ],

                'download_xlsx' => [
                    'label' => 'הורדת קובץ .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'ייצוא גדול מידי',
            'body' => 'You may not export more than 1 row at once.|You may not export more than :count rows at once.',
        ],

        'started' => [
            'title' => 'ייצוא התחיל',
            'body' => 'פעולת הייצוא התחילה, שורה אחת תעובד ברקע. תוצג התראה כאשר קישור ההורדה יהיה מוכן.|פעולת הייצוא התחילה, :count שורות יעובדו ברקע. תוצג התראה כאשר קישור ההורדה יהיה מוכן.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

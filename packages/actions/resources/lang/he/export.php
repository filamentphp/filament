<?php

return [

    'label' => 'ייצוא :label',

    'modal' => [

        'heading' => 'ייצוא :label',

        'form' => [

            'columns' => [

                'label' => 'עמודות',

                'actions' => [

                    'select_all' => [
                        'label' => 'בחר הכל',
                    ],

                    'deselect_all' => [
                        'label' => 'בטל בחירה של הכל',
                    ],

                ],

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
            'body' => 'אין לייצא יותר משורה אחת בבת אחת.|אין לייצא יותר מ-:count שורות בבת אחת.',
        ],

        'no_columns' => [
            'title' => 'לא נבחרו עמודות',
            'body' => 'יש לבחור לפחות עמודה אחת כדי לייצא.',
        ],

        'started' => [
            'title' => 'ייצוא התחיל',
            'body' => 'פעולת הייצוא התחילה, שורה אחת תעובד ברקע. תוצג התראה כאשר קישור ההורדה יהיה מוכן.|פעולת הייצוא התחילה, :count שורות יעובדו ברקע. תוצג התראה כאשר קישור ההורדה יהיה מוכן.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

<?php

return [

    'label' => 'ייבוא :label',

    'modal' => [

        'heading' => 'ייבוא :label',

        'form' => [

            'file' => [

                'label' => 'קובץ',

                'placeholder' => 'העלה קובץ CSV',

                'rules' => [
                    'duplicate_columns' => '{0} The file must not contain more than one empty column header.|{1,*} The file must not contain duplicate column headers: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'עמודות',
                'placeholder' => 'בחר עמודה',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'הורד קובץ CSV לדוגמה',
            ],

            'import' => [
                'label' => 'ייבא',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'ייבוא הושלם',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'הורד מידע על השורה שנכשלה|הורד מידע על השורות שנכשלו',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'קובץ ה CSV שהועלה גדול מידי',
            'body' => 'אין לייבא יותר משורה אחת בבת אחת.|אין לייבא יותר מ :count שורות בבת אחת.',
        ],

        'started' => [
            'title' => 'ייבוא התחיל',
            'body' => 'הייבוא התחיל והעיבוד של שורה אחת יתבצע ברקע.|הייבוא התחיל והעיבוד של :count שורות יתבצע ברקע.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'שגיאה',
        'system_error' => 'שגיאה מערכת, נא צור קשר עם התמיכה.',
        'column_mapping_required_for_new_record' => 'העמודה :attribute לא מופתה לעמודה בקובץ, אך היא נדרשת ליצירת רשומות חדשות.',
    ],

];

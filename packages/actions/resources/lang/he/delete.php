<?php

return [

    'single' => [

        'label' => 'מחיקה',

        'modal' => [

            'heading' => 'מחיקת :label',

            'actions' => [

                'delete' => [
                    'label' => 'מחק',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'נמחק',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'מחק נבחרים',

        'modal' => [

            'heading' => 'מחק את ה:label שנבחרו',

            'actions' => [

                'delete' => [
                    'label' => 'מחק',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'נמחק',
            ],

            'deleted_partial' => [
                'title' => 'נמחקו :count מתוך :total',
                'missing_authorization_failure_message' => 'אין לך הרשאה למחוק :count.',
                'missing_processing_failure_message' => 'לא ניתן היה למחוק :count.',
            ],

            'deleted_none' => [
                'title' => 'המחיקה נכשלה',
                'missing_authorization_failure_message' => 'אין לך הרשאה למחוק :count.',
                'missing_processing_failure_message' => 'לא ניתן היה למחוק :count.',
            ],

        ],

    ],

];

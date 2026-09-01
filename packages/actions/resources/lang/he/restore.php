<?php

return [

    'single' => [

        'label' => 'שחזור',

        'modal' => [

            'heading' => 'שחזור :label',

            'actions' => [

                'restore' => [
                    'label' => 'שחזור',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'שוחזר',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'שחזר את הנבחרים',

        'modal' => [

            'heading' => 'שוחזרו הנבחרים מ :label',

            'actions' => [

                'restore' => [
                    'label' => 'שחזר',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'שוחזר',
            ],

            'restored_partial' => [
                'title' => 'שוחזרו :count מתוך :total',
                'missing_authorization_failure_message' => 'אין לך הרשאה לשחזר :count.',
                'missing_processing_failure_message' => 'לא ניתן היה לשחזר :count.',
            ],

            'restored_none' => [
                'title' => 'השחזור נכשל',
                'missing_authorization_failure_message' => 'אין לך הרשאה לשחזר :count.',
                'missing_processing_failure_message' => 'לא ניתן היה לשחזר :count.',
            ],

        ],

    ],

];

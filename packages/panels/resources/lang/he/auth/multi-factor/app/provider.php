<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'אפליקציית אימות',

            'below_content' => 'השתמש באפליקציה מאובטחת ליצירת קוד זמני לאימות ההתחברות.',

            'messages' => [
                'enabled' => 'מופעל',
                'disabled' => 'מושבת',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'השתמש בקוד מאפליקציית האימות שלך',

        'code' => [

            'label' => 'הזן את הקוד בן 6 הספרות מאפליקציית האימות',

            'validation_attribute' => 'קוד',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'השתמש בקוד שחזור במקום זאת',
                ],

            ],

            'messages' => [

                'invalid' => 'הקוד שהזנת אינו תקין.',

            ],

        ],

        'recovery_code' => [

            'label' => 'או הזן קוד שחזור',

            'validation_attribute' => 'קוד שחזור',

            'messages' => [

                'invalid' => 'קוד השחזור שהזנת אינו תקין.',

            ],

        ],

    ],

];

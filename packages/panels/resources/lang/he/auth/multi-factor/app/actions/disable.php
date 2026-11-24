<?php

return [

    'label' => 'כיבוי',

    'modal' => [

        'heading' => 'השבתת אפליקציית האימות',

        'description' => 'האם אתה בטוח שברצונך להפסיק להשתמש באפליקציית האימות? השבתה תסיר שכבת אבטחה נוספת מהחשבון שלך.',

        'form' => [
            'password' => [
                'label' => 'סיסמה נוכחית',
                'validation_attribute' => 'סיסמה נוכחית',
            ],

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

                    'rate_limited' => 'יותר מדי ניסיונות. נסה שוב מאוחר יותר.',

                ],

            ],

            'recovery_code' => [

                'label' => 'או הזן קוד שחזור',

                'validation_attribute' => 'קוד שחזור',

                'messages' => [

                    'invalid' => 'קוד השחזור שהזנת אינו תקין.',

                    'rate_limited' => 'יותר מדי ניסיונות. נסה שוב מאוחר יותר.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'השבת את אפליקציית האימות',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'אפליקציית האימות הושבתה',
        ],

    ],

];

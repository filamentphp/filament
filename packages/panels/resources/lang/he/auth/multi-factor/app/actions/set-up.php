<?php

return [

    'label' => 'הגדרה',

    'modal' => [

        'heading' => 'הגדרת אפליקציית אימות',

        'description' => <<<'BLADE'
            תזדקק לאפליקציה כמו Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) כדי להשלים את התהליך.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'סרוק את קוד ה-QR הזה באמצעות אפליקציית האימות שלך:',

                'alt' => 'קוד QR לסריקה באמצעות אפליקציית אימות',

            ],

            'text_code' => [

                'instruction' => 'או הזן את הקוד הזה ידנית:',

                'messages' => [
                    'copied' => 'הועתק',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'שמור את קודי השחזור הבאים במקום בטוח. הם יוצגו פעם אחת בלבד, אך תזדקק להם אם תאבד גישה לאפליקציית האימות שלך:',

            ],

        ],

        'form' => [

            'password' => [
                'label' => 'סיסמה נוכחית',
                'validation_attribute' => 'סיסמה נוכחית',
            ],

            'code' => [

                'label' => 'הזן את הקוד בן 6 הספרות מאפליקציית האימות',

                'validation_attribute' => 'קוד',

                'below_content' => 'תצטרך להזין את הקוד בן 6 הספרות מאפליקציית האימות בכל פעם שתתחבר או שתבצע פעולות רגישות.',

                'messages' => [

                    'invalid' => 'הקוד שהזנת אינו תקין.',

                    'rate_limited' => 'יותר מדי ניסיונות. נסה שוב מאוחר יותר.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'הפעל את אפליקציית האימות',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'אפליקציית האימות הופעלה',
        ],

    ],

];

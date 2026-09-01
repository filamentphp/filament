<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Autentiseringsapp',

            'below_content' => 'Använd en säker app för att generera en tillfällig kod för inloggningsverifiering.',

            'messages' => [
                'enabled' => 'Aktiverad',
                'disabled' => 'Inaktiverad',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Använd en kod från din autentiseringsapp',

        'code' => [

            'label' => 'Ange den 6-siffriga koden från autentiseringsappen',

            'validation_attribute' => 'kod',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Använd en återställningskod istället',
                ],

            ],

            'messages' => [

                'invalid' => 'Koden du angav är ogiltig.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Eller, ange en återställningskod',

            'validation_attribute' => 'återställningskod',

            'messages' => [

                'invalid' => 'Återställningskoden du angav är ogiltig.',

            ],

        ],

    ],

];

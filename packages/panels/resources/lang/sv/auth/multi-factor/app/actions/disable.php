<?php

return [

    'label' => 'Stäng av',

    'modal' => [

        'heading' => 'Inaktivera autentiseringsapp',

        'description' => 'Är du säker på att du vill sluta använda autentiseringsappen? Att inaktivera detta tar bort ett extra säkerhetsskikt från ditt konto.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Inaktivera autentiseringsapp',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Autentiseringsapp har inaktiverats',
        ],

    ],

];

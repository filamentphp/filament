<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Verificatiecodes per e-mail',

            'below_content' => 'Ontvang een tijdelijke code op je e-mailadres om je identiteit te verifiëren tijdens het inloggen.',

            'messages' => [
                'enabled' => 'Ingeschakeld',
                'disabled' => 'Uitgeschakeld',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Stuur een code naar je e-mail',

        'code' => [

            'label' => 'Voer de 6-cijferige code in die we je per e-mail hebben gestuurd',

            'validation_attribute' => 'code',

            'actions' => [

                'resend' => [

                    'label' => 'Stuur een nieuwe code per e-mail',

                    'notifications' => [

                        'resent' => [
                            'title' => 'We hebben je een nieuwe code per e-mail gestuurd',
                        ],

                        'throttled' => [
                            'title' => 'Je hebt te vaak om een nieuwe code gevraagd. Wacht even en probeer het daarna opnieuw.',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'De ingevoerde code is ongeldig.',

            ],

        ],

    ],

];

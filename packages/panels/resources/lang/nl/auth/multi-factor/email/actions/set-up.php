<?php

return [

    'label' => 'Instellen',

    'modal' => [

        'heading' => 'Verificatiecodes per e-mail instellen',

        'description' => 'Je moet de 6-cijferige code die we je per e-mail sturen invoeren telkens als je inlogt of gevoelige acties uitvoert. Controleer je e-mail voor een 6-cijferige code om de installatie te voltooien.',

        'form' => [

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

                    'rate_limited' => 'Te veel pogingen. Probeer het later opnieuw.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Verificatiecodes per e-mail inschakelen',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Verificatiecodes per e-mail zijn ingeschakeld',
        ],

    ],

];

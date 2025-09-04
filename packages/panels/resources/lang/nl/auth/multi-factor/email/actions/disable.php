<?php

return [

    'label' => 'Uitschakelen',

    'modal' => [

        'heading' => 'Verificatiecodes per e-mail uitschakelen',

        'description' => 'Weet je zeker dat je wilt stoppen met het ontvangen van verificatiecodes per e-mail? Door dit uit te schakelen, verwijder je een extra beveiligingslaag van je account.',

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

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'De ingevoerde code is ongeldig.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Verificatiecodes per e-mail uitschakelen',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Verificatiecodes per e-mail zijn uitgeschakeld',
        ],

    ],

];

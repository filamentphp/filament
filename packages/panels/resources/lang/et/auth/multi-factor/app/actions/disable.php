<?php

return [

    'label' => 'Keela',

    'modal' => [

        'heading' => 'Keela autentimise rakendus',

        'description' => 'Kas olete kindel, et soovite autentimise rakenduse kasutamise lõpetada? Selle keelamine eemaldab teie kontolt täiendava turvakihi.',

        'form' => [

            'code' => [

                'label' => 'Sisestage autentimise rakendusest saadud 6-kohaline kood',

                'validation_attribute' => 'kood',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Kasutage selle asemel taastamiskoodi',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Sisestatud kood on vale.',

                    'rate_limited' => 'Liiga palju katseid. Palun proovige hiljem uuesti.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Või sisestage taastamiskood',

                'validation_attribute' => 'taastamiskood',

                'messages' => [

                    'invalid' => 'Sisestatud taastamiskood on vale.',

                    'rate_limited' => 'Liiga palju katseid. Palun proovige hiljem uuesti.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Keela autentimise rakendus',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Autentimise rakendus on keelatud',
        ],

    ],

];

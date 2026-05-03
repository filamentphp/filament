<?php

return [

    'label' => 'Seadistamine',

    'modal' => [

        'heading' => 'Seadistage e-posti kinnituskoodid',

        'description' => 'Iga kord, kui sisse logite või teete tundlikke toiminguid, peate sisestama 6-kohalise koodi, mille me teile e-posti teel saadame. Kontrollige oma e-posti, et saada 6-kohaline kood seadistamise lõpuleviimiseks.',

        'form' => [

            'code' => [

                'label' => 'Sisestage 6-kohaline kood, mille me teile e-posti teel saatsime',

                'validation_attribute' => 'kood',

                'actions' => [

                    'resend' => [

                        'label' => 'Saada uus kood e-posti teel',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Oleme saatnud teile uue koodi e-posti teel',
                            ],

                            'throttled' => [
                                'title' => 'Liiga palju uuesti saatmise katseid. Palun oodake enne uue koodi küsimist.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Sisestatud kood on vale.',

                    'rate_limited' => 'Liiga palju katseid. Palun proovige hiljem uuesti.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Luba e-posti kinnituskoodid',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'E-posti kinnituskoodid on lubatud',
        ],

    ],

];

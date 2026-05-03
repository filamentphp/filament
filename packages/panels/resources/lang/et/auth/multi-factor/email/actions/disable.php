<?php

return [

    'label' => 'Keela',

    'modal' => [

        'heading' => 'Keela e-posti kinnituskoodid',

        'description' => 'Kas olete kindel, et soovite lõpetada e-posti kinnituskoodide saamise? Selle keelamine eemaldab teie kontolt täiendava turvakihi.',

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
                'label' => 'Keela e-posti kinnituskoodid',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'E-posti kinnituskoodid on keelatud',
        ],

    ],

];

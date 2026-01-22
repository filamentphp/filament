<?php

return [

    'label' => 'Pois päältä',

    'modal' => [

        'heading' => 'Ota sähköpostin vahvistuskoodit pois käytöstä',

        'description' => 'Oletko varma että haluat lopettaa sähköpostin vahvistuskoodien käytön? Sen ottaminen pois päältä heikentää tilisi turvallisuutta.',

        'form' => [

            'code' => [

                'label' => 'Syötä 6-merkkinen koodi jonka lähetimme sähköpostiisi',

                'validation_attribute' => 'koodi',

                'actions' => [

                    'resend' => [

                        'label' => 'Lähetä uusi koodi sähköpostitse',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Olemme lähettänyt koodin sähköpostiisi',
                            ],

                            'throttled' => [
                                'title' => 'Liian monta lähetyksen yritystä. Odota ennen seuraavaa yritystä.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Annettu koodi on väärin.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Ota sähköpostin vahvistuskoodit pois käytöstä',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Sähköpostin vahvistuskoodit on otettu pois käytöstä',
        ],

    ],

];

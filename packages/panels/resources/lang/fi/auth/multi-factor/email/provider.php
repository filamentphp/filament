<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Sähköpostin vahvistuskoodit',

            'below_content' => 'Vastaanota tilapäinen koodi sähköpostiisi jolla vahvistat henkilöllisyytesi kirjautumisen yhteydessä.',

            'messages' => [
                'enabled' => 'Käytössä',
                'disabled' => 'Pois käytöstä',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Lähetä koodi sähköpostiisi',

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

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Annettu koodi on väärin.',

            ],

        ],

    ],

];

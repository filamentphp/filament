<?php

return [

    'label' => 'Set up',

    'modal' => [

        'heading' => 'Ota sähköpostin vahvistuskoodit käyttöön',

        'description' => 'Tarvitset 6-merkkisen koodin jonka lähetämme sähköpostitse joka kerta kun kirjaudut tai suoritat arkaluonteisia toimintoja. Tarkista sähköpostisi 6-merkkistä koodia jolla viimeistelet käyttöönoton.',

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
                'label' => 'Ota sähköpostin vahvistuskoodit käyttöön',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Sähköpostin vahvistuskoodit on otettu käyttöön',
        ],

    ],

];

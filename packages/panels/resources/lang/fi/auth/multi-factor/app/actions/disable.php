<?php

return [

    'label' => 'Pois päältä',

    'modal' => [

        'heading' => 'Ota todennussovellus pois käytöstä',

        'description' => 'Oletko varma että haluat lopettaa todennussovelluksen käytön? Sen ottaminen pois päältä heikentää tilisi turvallisuutta.',

        'form' => [

            'code' => [

                'label' => 'Syötä todennussovelluksen antama koodi',

                'validation_attribute' => 'koodi',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Käytä palautuskoodia',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Annettu koodi on väärin.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Tai, anna palautuskoodi',

                'validation_attribute' => 'palautuskoodi',

                'messages' => [

                    'invalid' => 'Annettu palautuskoodi on väärin.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Ota todennussovellus pois käytöstä',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Todennussovellus on otettu pois käytöstä',
        ],

    ],

];

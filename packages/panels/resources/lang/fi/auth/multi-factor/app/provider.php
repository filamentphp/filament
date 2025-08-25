<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Todennussovellus',

            'below_content' => 'Käytä turvallista sovellusta tilapäisten koodien luomiseen kirjautumista varten.',

            'messages' => [
                'enabled' => 'Käytössä',
                'disabled' => 'Pois käytöstä',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Käytä koodia todennussovelluksesta',

        'code' => [

            'label' => 'Anna 6-merkkinen koodi todennussovelluksesta',

            'validation_attribute' => 'koodi',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Käytä palautuskoodia',
                ],

            ],

            'messages' => [

                'invalid' => 'Antamasi koodi on viallinen.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Tai, anna palautuskoodi',

            'validation_attribute' => 'palautuskoodi',

            'messages' => [

                'invalid' => 'Antamasi palautuskoodi on viallinen.',

            ],

        ],

    ],

];

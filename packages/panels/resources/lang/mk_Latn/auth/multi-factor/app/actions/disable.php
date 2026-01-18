<?php

return [

    'label' => 'Iskluči',

    'modal' => [

        'heading' => 'Onevozmoži aplikacija za avtentifikacija',

        'description' => 'Dali ste sigurni deka sakate da prestanete da ja koristite aplikаcijata za avtentifikacija? Onevozmožuvanjeto na ova ќe go otstаni dopolnitelniot sloj na bezbednost od vašata smetka.',

        'form' => [

            'code' => [

                'label' => 'Vnesete go 6-cifreniot kod od aplikаcijata za avtentifikacija',

                'validation_attribute' => 'kod',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Koristete kod za obnovуvanje namesto toa',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Kodot što go vnesovte ne e validen.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Ili, vnesete kod za obnovуvanje',

                'validation_attribute' => 'kod za obnovуvanje',

                'messages' => [

                    'invalid' => 'Kodot za obnovуvanje što go vnesovte ne e validen.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Onevozmoži aplikаcija za avtentifikacija',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Aplikаcijata za avtentifikacija e onevozmožena',
        ],

    ],

];

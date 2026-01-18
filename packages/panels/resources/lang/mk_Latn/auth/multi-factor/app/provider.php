<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Aplikаcija za avtentifikacija',

            'below_content' => 'Koristete bezbedna aplikаcija za da generirаte privremen kod za verifikacija pri najavuvanje.',

            'messages' => [
                'enabled' => 'Ovozmoženo',
                'disabled' => 'Onevozmoženo',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Koristete kod od vašata aplikаcija za avtentifikacija',

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

];

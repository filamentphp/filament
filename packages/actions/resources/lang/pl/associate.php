<?php

return [

    'single' => [

        'label' => 'Powiąż',

        'modal' => [

            'heading' => 'Powiąż :label',

            'fields' => [

                'record_id' => [
                    'label' => 'Rekord',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => 'Powiąż',
                ],

                'associate_another' => [
                    'label' => 'Powiąż i powiąż kolejny',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => 'Utworzono powiązanie',
            ],

        ],

    ],

];

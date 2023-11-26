<?php

return [

    'single' => [

        'label' => 'Koppla',

        'modal' => [

            'heading' => 'Koppla :label',

            'fields' => [

                'record_id' => [
                    'label' => 'Objekt',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => 'Koppla',
                ],

                'associate_another' => [
                    'label' => 'Koppla & koppla en till',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => 'Kopplades',
            ],

        ],

    ],

];

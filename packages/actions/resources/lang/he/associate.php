<?php

return [

    'single' => [

        'label' => 'שיוך',

        'modal' => [

            'heading' => 'שייך את :label',

            'fields' => [

                'record_id' => [
                    'label' => 'רשומה',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => 'שייך',
                ],

                'associate_another' => [
                    'label' => 'שייך ושייך אחד נוסף',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => 'שויך',
            ],

        ],

    ],

];

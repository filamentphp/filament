<?php

return [

    'single' => [

        'label' => 'Подключить',

        'modal' => [

            'heading' => 'Подключить :label',

            'fields' => [

                'record_id' => [
                    'label' => 'Запись',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => 'Подключить',
                ],

                'associate_another' => [
                    'label' => 'Подключить и Подключить другое',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => 'Подключено',
            ],

        ],

    ],

];

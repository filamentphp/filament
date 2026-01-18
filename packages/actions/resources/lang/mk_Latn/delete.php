<?php

return [

    'single' => [

        'label' => 'Izbriši',

        'modal' => [

            'heading' => 'Izbriši :label',

            'actions' => [

                'delete' => [
                    'label' => 'Izbriši',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Izbrišano',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Izbriši izabrani',

        'modal' => [

            'heading' => 'Izbriši izabrani :label',

            'actions' => [

                'delete' => [
                    'label' => 'Izbriši',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Izbrišano',
            ],

            'deleted_partial' => [
                'title' => 'Izbrišani :count od :total',
                'missing_authorization_failure_message' => 'Nemate dozvola da izbrišete :count.',
                'missing_processing_failure_message' => ':count ne možaa da bidat izbrišani.',
            ],

            'deleted_none' => [
                'title' => 'Neuspešno brishenje',
                'missing_authorization_failure_message' => 'Nemate dozvola da izbrišete :count.',
                'missing_processing_failure_message' => ':count ne možaa da bidat izbrišani.',
            ],

        ],

    ],

];

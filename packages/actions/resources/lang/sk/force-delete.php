<?php

return [

    'single' => [

        'label' => 'Trvalo odstrániť',

        'modal' => [

            'heading' => 'Trvalo odstrániť :label',

            'actions' => [

                'delete' => [
                    'label' => 'Odstrániť',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Odstránené',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Trvalo odstrániť vybrané',

        'modal' => [

            'heading' => 'Trvalo odstrániť vybrané :label',

            'actions' => [

                'delete' => [
                    'label' => 'Odstránené',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Odstránené',
            ],

            'deleted_partial' => [
                'title' => 'Odstránených :count z :total',
                'missing_authorization_failure_message' => 'Nemáte oprávnenie odstrániť :count.',
                'missing_processing_failure_message' => ':count sa nepodarilo odstrániť.',
            ],

            'deleted_none' => [
                'title' => 'Nepodarilo sa odstrániť',
                'missing_authorization_failure_message' => 'Nemáte oprávnenie odstrániť :count.',
                'missing_processing_failure_message' => ':count sa nepodarilo odstrániť.',
            ],

        ],

    ],

];

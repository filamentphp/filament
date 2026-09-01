<?php

return [

    'single' => [

        'label' => 'Odstrániť',

        'modal' => [

            'heading' => 'Odstrániť :label',

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

        'label' => 'Odstrániť vybrané',

        'modal' => [

            'heading' => 'Odstrániť vybrané :label',

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

            'deleted_partial' => [
                'title' => 'Odstránených :count z :total',
                'missing_authorization_failure_message' => 'Nemáte oprávnenie odstrániť :count.',
                'missing_processing_failure_message' => ':count sa nepodarilo odstrániť.',
            ],

            'deleted_none' => [
                'title' => 'Odstránenie zlyhalo',
                'missing_authorization_failure_message' => 'Nemáte oprávnenie odstrániť :count.',
                'missing_processing_failure_message' => ':count sa nepodarilo odstrániť.',
            ],

        ],

    ],

];

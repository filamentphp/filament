<?php

return [

    'single' => [

        'label' => 'Behartu ezabatzera',

        'modal' => [

            'heading' => 'Behartu :label ezabatzera',

            'actions' => [

                'delete' => [
                    'label' => 'Ezabatu',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Ezabatuta',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Hautatuak behartu ezabatzera',

        'modal' => [

            'heading' => 'Hautatutako :label behartu ezabatzera',

            'actions' => [

                'delete' => [
                    'label' => 'Ezabatu',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Ezabatuta',
            ],

            'deleted_partial' => [
                'title' => ':count / :total ezabatu dira',
                'missing_authorization_failure_message' => 'Ez duzu baimenik :count ezabatzeko.',
                'missing_processing_failure_message' => ':count ezin izan dira ezabatu.',
            ],

            'deleted_none' => [
                'title' => 'Ezin izan da ezabatu',
                'missing_authorization_failure_message' => 'Ez duzu baimenik :count ezabatzeko.',
                'missing_processing_failure_message' => ':count ezin izan dira ezabatu.',
            ],

        ],

    ],

];

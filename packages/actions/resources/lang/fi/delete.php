<?php

return [

    'single' => [

        'label' => 'Poista',

        'modal' => [

            'heading' => 'Poista :label',

            'actions' => [

                'delete' => [
                    'label' => 'Poista',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Poistettu',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Poista valitut',

        'modal' => [

            'heading' => 'Poista valitut :label',

            'actions' => [

                'delete' => [
                    'label' => 'Poista',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Poistettu',
            ],

            'deleted_partial' => [
                'title' => 'Poistettu :count / :total',
                'missing_authorization_failure_message' => 'Sinulla ei ole oikeuksia poistaa :count.',
                'missing_processing_failure_message' => ':count ei voitu poistaa.',
            ],

            'deleted_none' => [
                'title' => 'Poistaminen epäonnistui',
                'missing_authorization_failure_message' => 'Sinulla ei ole oikeuksia poistaa :count.',
                'missing_processing_failure_message' => ':count ei voitu poistaa.',
            ],

        ],

    ],

];

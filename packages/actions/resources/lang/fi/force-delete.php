<?php

return [

    'single' => [

        'label' => 'Poista lopullisesti',

        'modal' => [

            'heading' => 'Pakota poistaminen :label',

            'actions' => [

                'delete' => [
                    'label' => 'Poista',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Tietue poistettu',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Poista valitut lopullisesti',

        'modal' => [

            'heading' => 'Poista valitut lopullisesti :label',

            'actions' => [

                'delete' => [
                    'label' => 'Poista',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Tietueet poistettu',
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

<?php

return [

    'single' => [

        'label' => 'Ištrinti',

        'modal' => [

            'heading' => 'Ištrinti :label',

            'actions' => [

                'delete' => [
                    'label' => 'Ištrinti',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Ištrinta',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Ištrinti pasirinktus',

        'modal' => [

            'heading' => 'Ištrinti pasirinktus :label',

            'actions' => [

                'delete' => [
                    'label' => 'Ištrinti',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Ištrinta',
            ],

            'deleted_partial' => [
                'title' => 'Ištrinta :count iš :total',
                'missing_authorization_failure_message' => 'Neturite leidimo ištrinti :count.',
                'missing_processing_failure_message' => ':count negalėjo būti ištrinta.',
            ],

            'deleted_none' => [
                'title' => 'Nepavyko ištrinti',
                'missing_authorization_failure_message' => 'Neturite leidimo ištrinti :count.',
                'missing_processing_failure_message' => ':count negalėjo būti ištrinta.',
            ],

        ],

    ],

];

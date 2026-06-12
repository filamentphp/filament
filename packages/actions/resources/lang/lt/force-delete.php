<?php

return [

    'single' => [

        'label' => 'Priverstinai ištrinti',

        'modal' => [

            'heading' => 'Priverstinai ištrinti :label',

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

        'label' => 'Priverstinai ištrinti pasirinktus',

        'modal' => [

            'heading' => 'Priverstinai ištrinti pasirinktus :label',

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

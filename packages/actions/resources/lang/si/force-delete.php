<?php

return [

    'single' => [

        'label' => 'බලහත්කාරයෙන් මකන්න',

        'modal' => [

            'heading' => ':label බලහත්කාරයෙන් මකන්න',

            'actions' => [

                'delete' => [
                    'label' => 'මකන්න',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'මකා දමන ලදී',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'තෝරාගත් ඒවා බලහත්කාරයෙන් මකන්න',

        'modal' => [

            'heading' => 'තෝරාගත් :label බලහත්කාරයෙන් මකන්න',

            'actions' => [

                'delete' => [
                    'label' => 'මකන්න',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'මකා දමන ලදී',
            ],

            'deleted_partial' => [
                'title' => ':total න් :count ක් මකා දමන ලදී',
                'missing_authorization_failure_message' => ':count ක් මැකීමට ඔබට අවසර නැත.',
                'missing_processing_failure_message' => ':count ක් මැකීමට නොහැකි විය.',
            ],

            'deleted_none' => [
                'title' => 'මැකීම අසාර්ථක විය',
                'missing_authorization_failure_message' => ':count ක් මැකීමට ඔබට අවසර නැත.',
                'missing_processing_failure_message' => ':count ක් මැකීමට නොහැකි විය.',
            ],

        ],

    ],

];

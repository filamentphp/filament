<?php

return [

    'single' => [

        'label' => 'ප්‍රතිෂ්ඨාපනය කරන්න',

        'modal' => [

            'heading' => ':label ප්‍රතිෂ්ඨාපනය කරන්න',

            'actions' => [

                'restore' => [
                    'label' => 'ප්‍රතිෂ්ඨාපනය කරන්න',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'ප්‍රතිෂ්ඨාපනය කරන ලදී',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'තෝරාගත් ඒවා ප්‍රතිෂ්ඨාපනය කරන්න',

        'modal' => [

            'heading' => 'තෝරාගත් :label ප්‍රතිෂ්ඨාපනය කරන්න',

            'actions' => [

                'restore' => [
                    'label' => 'ප්‍රතිෂ්ඨාපනය කරන්න',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'ප්‍රතිෂ්ඨාපනය කරන ලදී',
            ],

            'restored_partial' => [
                'title' => ':total න් :count ක් ප්‍රතිෂ්ඨාපනය කරන ලදී',
                'missing_authorization_failure_message' => ':count ක් ප්‍රතිෂ්ඨාපනය කිරීමට ඔබට අවසර නැත.',
                'missing_processing_failure_message' => ':count ක් ප්‍රතිෂ්ඨාපනය කිරීමට නොහැකි විය.',
            ],

            'restored_none' => [
                'title' => 'ප්‍රතිෂ්ඨාපනය අසාර්ථක විය',
                'missing_authorization_failure_message' => ':count ක් ප්‍රතිෂ්ඨාපනය කිරීමට ඔබට අවසර නැත.',
                'missing_processing_failure_message' => ':count ක් ප්‍රතිෂ්ඨාපනය කිරීමට නොහැකි විය.',
            ],

        ],

    ],

];

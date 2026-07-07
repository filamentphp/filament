<?php

return [

    'label' => 'Detach tags',

    'modal' => [

        'heading' => 'Detach tags from selected :label',

        'form' => [

            'tags' => [
                'label' => 'Tags',
            ],

        ],

        'actions' => [

            'detach' => [
                'label' => 'Detach',
            ],

        ],

    ],

    'notifications' => [

        'detached' => [
            'title' => 'Detached tags',
        ],

        'detached_partial' => [
            'title' => 'Detached tags from :count of :total',
            'missing_authorization_failure_message' => 'You don\'t have permission to detach tags from :count.',
            'missing_processing_failure_message' => 'Tags could not be detached from :count.',
        ],

        'detached_none' => [
            'title' => 'Failed to detach tags',
            'missing_authorization_failure_message' => 'You don\'t have permission to detach tags from :count.',
            'missing_processing_failure_message' => 'Tags could not be detached from :count.',
        ],

    ],

];

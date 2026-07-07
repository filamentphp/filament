<?php

return [

    'label' => 'Manage tags',

    'modal' => [

        'heading' => 'Manage tags for selected :label',

        'form' => [

            'tags_to_attach' => [
                'label' => 'Tags to attach',
            ],

            'tags_to_detach' => [
                'label' => 'Tags to detach',
                'validation' => [
                    'attached_and_detached' => 'The following tags cannot be attached and detached at the same time: :tags.',
                ],
            ],

        ],

        'actions' => [

            'save' => [
                'label' => 'Save',
            ],

        ],

    ],

    'notifications' => [

        'updated' => [
            'title' => 'Updated tags',
        ],

        'updated_partial' => [
            'title' => 'Updated tags for :count of :total',
            'missing_authorization_failure_message' => 'You don\'t have permission to update tags for :count.',
            'missing_processing_failure_message' => 'Tags could not be updated for :count.',
        ],

        'updated_none' => [
            'title' => 'Failed to update tags',
            'missing_authorization_failure_message' => 'You don\'t have permission to update tags for :count.',
            'missing_processing_failure_message' => 'Tags could not be updated for :count.',
        ],

    ],

];

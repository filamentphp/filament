<?php

return [

    'label' => 'Attach tags',

    'modal' => [

        'heading' => 'Attach tags to selected :label',

        'form' => [

            'tags' => [
                'label' => 'Tags',
            ],

        ],

        'actions' => [

            'attach' => [
                'label' => 'Attach',
            ],

        ],

    ],

    'notifications' => [

        'attached' => [
            'title' => 'Attached tags',
        ],

        'attached_partial' => [
            'title' => 'Attached tags to :count of :total',
            'missing_authorization_failure_message' => 'You don\'t have permission to attach tags to :count.',
            'missing_processing_failure_message' => 'Tags could not be attached to :count.',
        ],

        'attached_none' => [
            'title' => 'Failed to attach tags',
            'missing_authorization_failure_message' => 'You don\'t have permission to attach tags to :count.',
            'missing_processing_failure_message' => 'Tags could not be attached to :count.',
        ],

    ],

];

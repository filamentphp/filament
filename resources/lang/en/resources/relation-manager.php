<?php

return [

    'buttons' => [

        'attach' => [
            'label' => 'Attach Existing',
        ],

        'create' => [
            'label' => 'New',
        ],

        'detach' => [
            'label' => 'Detach selected'
        ],

    ],

    'modals' => [

        'attach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancel',
                ],

                'attach' => [
                    'label' => 'Attach',
                ],

                'attachAnother' => [
                    'label' => 'Attach & Attach Another',
                ],

            ],

            'form' => [

                'related' => [
                    'placeholder' => 'Start typing to search...',
                ],

            ],

            'heading' => 'Attach Existing',

            'messages' => [
                'attached' => 'Attached!',
            ],

        ],

        'create' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancel',
                ],

                'create' => [
                    'label' => 'Create',
                ],

                'createAnother' => [
                    'label' => 'Create & Create Another',
                ],

            ],

            'heading' => 'Create',

            'messages' => [
                'created' => 'Created!',
            ],

        ],

        'detach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancel',
                ],

                'detach' => [
                    'label' => 'Detach selected',
                ],

            ],

            'description' => 'Are you sure you would like to detach the selected records? This action cannot be undone.',

            'heading' => 'Detach the selected records? ',

            'messages' => [
                'detached' => 'Detached!',
            ],

        ],

        'edit' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancel',
                ],

                'save' => [
                    'label' => 'Save',
                ],

            ],

            'heading' => 'Edit',

            'messages' => [
                'saved' => 'Saved!',
            ],

        ],

    ],

];

<?php

return [

    'title' => 'Edit :label',

    'breadcrumb' => 'Edit',

    'actions' => [

        'delete' => [

            'label' => 'Delete',

            'modal' => [

                'heading' => 'Delete :label',

                'subheading' => 'Are you sure you would like to do this?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Delete',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => 'Deleted',
            ],

        ],

        'view' => [
            'label' => 'View',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Cancel',
            ],

            'save' => [
                'label' => 'Save',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Saved',
    ],

];

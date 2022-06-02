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

        'force_delete' => [

            'label' => 'Force delete',

            'modal' => [

                'heading' => 'Force delete :label',

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



        'restore' => [

            'label' => 'Restore',

            'modal' => [

                'heading' => 'Restore :label',

                'subheading' => 'Are you sure you would like to do this?',

                'buttons' => [

                    'restore' => [
                        'label' => 'Restore',
                    ],

                ],

            ],

            'messages' => [
                'restored' => 'Restored',
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

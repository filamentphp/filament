<?php

return [

    'breadcrumb' => 'List',

    'actions' => [

        'create' => [

            'label' => 'New :label',

            'modal' => [

                'heading' => 'Create :label',

                'actions' => [

                    'create' => [
                        'label' => 'Create',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Create & create another',
                    ],

                ],

            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [
                'label' => 'Delete',
            ],

            'edit' => [

                'label' => 'Edit',

                'modal' => [

                    'heading' => 'Edit :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Save',
                        ],

                    ],

                ],

            ],

            'view' => [
                'label' => 'View',
            ],

        ],

        'bulk_actions' => [

            'delete' => [
                'label' => 'Delete selected',
            ],

        ],

    ],

];

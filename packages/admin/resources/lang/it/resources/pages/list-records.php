<?php

return [

    'breadcrumb' => 'Lista',

    'actions' => [

        'create' => [
            'label' => 'Nuovo :label',

            'modal' => [

                'heading' => 'Nuovo :label',

                'actions' => [

                    'create' => [
                        'label' => 'Nuovo',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Nuovo & un altro nuovo',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Creato',
            ],
        ],

    ],

    'table' => [

        'actions' => [
            'delete' => [

                'label' => 'Elimina',

                'modal' => [
                    'heading' => 'Elimina :label',
                ],

                'messages' => [
                    'deleted' => 'Eliminato',
                ],

            ],
            'edit' => [
                'label' => 'Modifica',

                'modal' => [

                    'heading' => 'Modifica :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Salva',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Salvato',
                ],
            ],

            'view' => [
                'label' => 'Visualizza',

                'modal' => [

                    'heading' => 'Visualizza :label',

                    'actions' => [

                        'close' => [
                            'label' => 'Chiudi',
                        ],

                    ],

                ],
            ],

        ],

        'bulk_actions' => [

            'delete' => [
                'label' => 'Elimina selezionato',
                'messages' => [
                    'deleted' => 'Eliminato',
                ],
            ],

        ],

    ],

];

<?php

return [

    'breadcrumb' => 'Liste',

    'actions' => [

        'create' => [

            'label' => 'Nouveau :label',

            'modal' => [

                'heading' => 'Nouveau :label',

                'actions' => [

                    'create' => [
                        'label' => 'Nouveau',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Créer & Ajouter un autre',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Crée(e)',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Supprimer',

                'messages' => [
                    'deleted' => 'Supprimé(e)',
                ],

            ],

            'edit' => [

                'label' => 'Modifier',

                'modal' => [

                    'heading' => 'Modifier :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Sauvegarder',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Sauvegardé(e)',
                ],

            ],

            'view' => [
                'label' => 'Voir',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Supprimer la sélection',

                'messages' => [
                    'deleted' => 'Supprimé(e)s',
                ],

            ],

        ],

    ],

];

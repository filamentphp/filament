<?php

return [

    'single' => [

        'label' => 'Supprimer définitivement',

        'modal' => [

            'heading' => 'Supprimer définitivement :label',

            'actions' => [

                'delete' => [
                    'label' => 'Supprimer',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Enregistrement supprimé',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Forcer la suppression de la sélection',

        'modal' => [

            'heading' => 'Forcer la suppression de la sélection de  :label',

            'actions' => [

                'delete' => [
                    'label' => 'Supprimer',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Enregistrements supprimés',
            ],

            'deleted_partial' => [
                'title' => 'Supprimé :count sur :total',
                'missing_authorization_failure_message' => 'Vous n\'avez pas la permission de supprimer :count.',
                'missing_processing_failure_message' => ':count n\'ont pas pu être supprimés.',
            ],

            'deleted_none' => [
                'title' => 'Échec de la suppression',
                'missing_authorization_failure_message' => 'Vous n\'avez pas la permission de supprimer :count.',
                'missing_processing_failure_message' => ':count n\'ont pas pu être supprimés.',
            ],

        ],

    ],

];

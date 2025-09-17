<?php

return [

    'single' => [

        'label' => 'Supprimer',

        'modal' => [

            'heading' => 'Supprimer :label',

            'actions' => [

                'delete' => [
                    'label' => 'Supprimer',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Supprimé(e)',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Supprimer la sélection',

        'modal' => [

            'heading' => 'Supprimer la sélection de :label',

            'actions' => [

                'delete' => [
                    'label' => 'Supprimer la sélection',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Supprimé(e)s',
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

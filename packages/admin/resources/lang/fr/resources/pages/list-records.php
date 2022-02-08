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

    ],

    'table' => [

        'actions' => [

            'edit' => [
                'label' => 'Modifier',
            ],

            'view' => [
                'label' => 'Voir',
            ],

        ],

        'bulk_actions' => [

            'delete' => [
                'label' => 'Supprimer la sélection',
            ],

        ],

    ],

];

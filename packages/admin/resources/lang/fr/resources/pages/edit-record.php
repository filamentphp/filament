<?php

return [

    'title' => 'Modifier :label',

    'breadcrumb' => 'Modifier',

    'actions' => [

        'delete' => [

            'label' => 'Supprimer',

            'modal' => [

                'heading' => 'Supprimer :label',

                'subheading' => 'Etes-vous sûr(e) de vouloir supprimer cette donnée ?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Supprimer',
                    ],

                ],

            ],

        ],

        'view' => [
            'label' => 'Voir',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Annuler',
            ],

            'save' => [
                'label' => 'Sauvegarder',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Sauvegardé',
    ],

];

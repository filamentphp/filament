<?php

return [

    'buttons' => [

        'attach' => [
            'label' => 'Joindre existant',
        ],

        'create' => [
            'label' => 'Nouveau',
        ],

        'detach' => [
            'label' => 'Détacher la sélection'
        ],

    ],

    'modals' => [

        'attach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annuler',
                ],

                'attach' => [
                    'label' => 'Joindre',
                ],

                'attachAnother' => [
                    'label' => 'Joindre et joindre un autre',
                ],

            ],

            'form' => [

                'related' => [
                    'placeholder' => 'Commencez à taper pour rechercher...',
                ],

            ],

            'heading' => 'Joindre existant',

            'messages' => [
                'attached' => 'Ci-joint!',
            ],

        ],

        'create' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annuler',
                ],

                'create' => [
                    'label' => 'Créer',
                ],

                'createAnother' => [
                    'label' => 'Créer et créer un autre',
                ],

            ],

            'heading' => 'Créer',

            'messages' => [
                'created' => 'Créé!',
            ],

        ],

        'detach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annuler',
                ],

                'detach' => [
                    'label' => 'Détacher la sélection',
                ],

            ],

            'description' => 'Voulez-vous vraiment détacher les enregistrements sélectionnés? Cette action ne peut pas être annulée.',

            'heading' => 'Détacher les enregistrements sélectionnés?',

            'messages' => [
                'detached' => 'Détaché!',
            ],

        ],

        'edit' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annuler',
                ],

                'save' => [
                    'label' => 'Sauvegarder',
                ],

            ],

            'heading' => 'Modifier',

            'messages' => [
                'saved' => 'Enregistré!',
            ],

        ],

    ],

];

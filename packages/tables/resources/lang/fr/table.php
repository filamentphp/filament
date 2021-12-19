<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Rechercher',
            'placeholder' => 'Rechercher',
        ],

    ],

    'pagination' => [

        'label' => 'Navigation par pagination',

        'overview' => 'Montrer :first à :last de :total résultats',

        'fields' => [

            'records_per_page' => [
                'label' => 'par page',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Aller à la page :page',
            ],

            'next' => [
                'label' => 'Suivant',
            ],

            'previous' => [
                'label' => 'Précédent',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filtre',
        ],

        'open_actions' => [
            'label' => 'Actions ouvertes',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Êtes-vous sûr de vouloir faire ça?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Annuler',
                ],

                'confirm' => [
                    'label' => 'Confirmer',
                ],

                'submit' => [
                    'label' => 'Envoyer',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'Aucun document trouvé',
    ],

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => 'Sélectionner tous les enregistrements',
            ],

        ],

    ],

];

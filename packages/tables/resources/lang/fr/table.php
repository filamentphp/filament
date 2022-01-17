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

        'overview' => 'Affichage de :first à :last sur :total éléments',

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

            'requires_confirmation_subheading' => 'Êtes-vous sûr de vouloir faire cela ?',

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
        'heading' => 'Aucun élément trouvé',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Réinitialiser',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Tout',
        ],

        'select' => [
            'placeholder' => 'Tout',
        ],

    ],

    'selection_indicator' => [

        'selected_count' => '1 élément sélectionné.|:count éléments sélectionnés.',

        'buttons' => [

            'select_all' => [
                'label' => 'Sélectionner tout (:count)',
            ],

            'deselect_all' => [
                'label' => 'Désélectionner tout',
            ],

        ],

    ],
];

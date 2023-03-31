<?php

return [

    'columns' => [

        'tags' => [
            'more' => ':count de plus',
        ],

        'messages' => [
            'copied' => 'Copié',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Sélectionner/déselectionner tous les éléments pour les actions groupées.',
        ],

        'bulk_select_record' => [
            'label' => "Sélectionner/désélectionner l'élément :key pour les actions groupées.",
        ],

        'search_query' => [
            'label' => 'Rechercher',
            'placeholder' => 'Rechercher',
        ],

    ],

    'pagination' => [

        'label' => 'Navigation par pagination',

        'overview' => '{1} Affichage de 1 résultat|[2,*] Affichage de :first à :last sur :total résultats',

        'fields' => [

            'records_per_page' => [

                'label' => 'par page',

                'options' => [
                    'all' => 'Tous',
                ],
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

        'disable_reordering' => [
            'label' => 'Fin du classement des enregistrements',
        ],

        'enable_reordering' => [
            'label' => 'Classer les enregistrements',
        ],

        'filter' => [
            'label' => 'Filtre',
        ],

        'open_actions' => [
            'label' => 'Actions ouvertes',
        ],

        'toggle_columns' => [
            'label' => 'Basculer les colonnes',
        ],

    ],

    'empty' => [

        'heading' => 'Aucun élément trouvé',

        'buttons' => [

            'reset_column_searches' => [
                'label' => 'Effacer la recherche de colonne',
            ],

        ],

    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Supprimer le filtre',
            ],

            'remove_all' => [
                'label' => 'Supprimer tous les filtres',
                'tooltip' => 'Supprimer tous les filtres',
            ],

            'reset' => [
                'label' => 'Réinitialiser les filtres',
            ],

        ],

        'indicator' => 'Filtres actifs',

        'multi_select' => [
            'placeholder' => 'Tout',
        ],

        'select' => [
            'placeholder' => 'Tout',
        ],

        'trashed' => [

            'label' => 'Enregistrements supprimés',

            'only_trashed' => 'Enregistrements supprimés uniquement',

            'with_trashed' => 'Avec les enregistrements supprimés',

            'without_trashed' => 'Sans les enregistrements supprimés',

        ],

    ],

    'reorder_indicator' => "Faites glisser et déposez les enregistrements dans l'ordre.",

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

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Trier par',
            ],

            'direction' => [

                'label' => 'Ordre',

                'options' => [
                    'asc' => 'Croissant',
                    'desc' => 'Décroissant',
                ],

            ],

        ],

    ],

];

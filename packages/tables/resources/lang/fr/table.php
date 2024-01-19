<?php

return [

    'column_toggle' => [

        'heading' => 'Colonnes',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Afficher :count de moins',
                'expand_list' => 'Afficher :count de plus',
            ],

            'more_list_items' => ':count de plus',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Sélectionner/déselectionner tous les éléments pour les actions groupées.',
        ],

        'bulk_select_record' => [
            'label' => "Sélectionner/désélectionner l'élément :key pour les actions groupées.",
        ],

        'bulk_select_group' => [
            'label' => 'Sélectionner/désélectionner le groupe :title pour les actions groupées.',
        ],

        'search' => [
            'label' => 'Rechercher',
            'placeholder' => 'Rechercher',
            'indicator' => 'Recherche',
        ],

    ],

    'summary' => [

        'heading' => 'Résumé',

        'subheadings' => [
            'all' => 'Tous :label',
            'group' => 'résumé de :group',
            'page' => 'Cette page',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Moyenne',
            ],

            'count' => [
                'label' => 'Compteur',
            ],

            'sum' => [
                'label' => 'Somme',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Fin du classement des enregistrements',
        ],

        'enable_reordering' => [
            'label' => 'Classer les enregistrements',
        ],

        'filter' => [
            'label' => 'Filtre',
        ],

        'group' => [
            'label' => 'Groupe',
        ],

        'open_bulk_actions' => [
            'label' => 'Ouvrir les actions',
        ],

        'toggle_columns' => [
            'label' => 'Basculer les colonnes',
        ],

    ],

    'empty' => [

        'heading' => 'Aucun élément trouvé',

        'description' => 'Créer un(e) :model pour commencer.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Appliquer les filtres',
            ],

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

        'heading' => 'Filtres',

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

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grouper par',
                'placeholder' => 'Grouper par',
            ],

            'direction' => [

                'label' => 'Groupe',

                'options' => [
                    'asc' => 'Croissant',
                    'desc' => 'Décroissant',
                ],

            ],

        ],

    ],

    'reorder_indicator' => "Faites glisser et déposez les enregistrements dans l'ordre.",

    'selection_indicator' => [

        'selected_count' => '1 élément sélectionné|:count éléments sélectionnés',

        'actions' => [

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

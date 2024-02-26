<?php

return [

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

    'actions' => [

        'go_to_page' => [
            'label' => 'Aller à la page :page',
        ],

        'next' => [
            'label' => 'Suivant',
        ],

        'previous' => [
            'label' => 'Précédent',
        ],

        'first' => [
            'label' => 'Première',
        ],

        'last' => [
            'label' => 'Dernière',
        ],

    ],

];

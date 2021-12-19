<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Cerca',
            'placeholder' => 'Cerca',
        ],

    ],

    'pagination' => [

        'label' => 'Navigazione paginazione',

        'overview' => 'Mostrati :first a :last di :total risultati',

        'fields' => [

            'records_per_page' => [
                'label' => 'per pagina',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Vai a pagina :page',
            ],

            'next' => [
                'label' => 'Successivo',
            ],

            'previous' => [
                'label' => 'Precedente',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filtra',
        ],

        'open_actions' => [
            'label' => 'Azioni aperte',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Sei sicuro di volerlo fare?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Annulla',
                ],

                'confirm' => [
                    'label' => 'Conferma',
                ],

                'submit' => [
                    'label' => 'Invia',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'Nessun valore trovato',
    ],

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => 'Seleziona tutti i :count',
            ],

        ],

    ],

];

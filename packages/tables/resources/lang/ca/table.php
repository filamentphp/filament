<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Cerca',
            'placeholder' => 'Cerca',
        ],

    ],

    'pagination' => [

        'label' => 'Paginació',

        'overview' => 'Mostrant :first a :last de :total resultatss',

        'fields' => [

            'records_per_page' => [
                'label' => 'per pàgina',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Anar a la pàgina :page',
            ],

            'next' => [
                'label' => 'Següent',
            ],

            'previous' => [
                'label' => 'Anterior',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filtre',
        ],

        'open_actions' => [
            'label' => 'Open actions',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Are you sure you would like to do this?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancela',
                ],

                'confirm' => [
                    'label' => 'Confirm',
                ],

                'submit' => [
                    'label' => 'Submit',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'No s\'han trobat registres.',
    ],

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => 'Select all :count',
            ],

        ],

    ],

];

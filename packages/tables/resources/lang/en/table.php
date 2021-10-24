<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Search',
            'placeholder' => 'Search',
        ],

    ],

    'pagination' => [

        'label' => 'Pagination Navigation',

        'overview' => 'Showing :first to :last of :total results',

        'fields' => [

            'records_per_page' => [
                'label' => 'per page',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Go to page :page',
            ],

            'next' => [
                'label' => 'Next',
            ],

            'previous' => [
                'label' => 'Previous',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filter',
        ],

    ],

    'actions' => [

        'delete' => [

            'button' => [
                'label' => 'Delete selected',
            ],

            'modal' => [

                'description' => 'Are you sure you would like to delete the selected records? This action cannot be undone.',

                'heading' => 'Delete the selected records?',

                'buttons' => [

                    'cancel' => [
                        'label' => 'Cancel',
                    ],

                    'delete' => [
                        'label' => 'Delete selected',
                    ],

                ],

            ],

        ],

        'actions' => [
            'placeholder' => 'Actions',
        ],

    ],

    'empty' => [
        'heading' => 'No records found',
    ],

];

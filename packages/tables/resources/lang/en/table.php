<?php

return [

    'delete' => [

        'button' => [
            'label' => 'Delete selected',
        ],

        'modal' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancel',
                ],

                'delete' => [
                    'label' => 'Delete selected',
                ],

            ],

            'description' => 'Are you sure you would like to delete the selected records? This action cannot be undone.',

            'heading' => 'Delete the selected records?',

        ],

    ],

    'filter' => [
        'placeholder' => 'Filter',
    ],

    'actions' => [
        'placeholder' => 'Actions',
    ],

    'messages' => [
        'noRecords' => 'No records found',
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

    'search' => [
        'placeholder' => 'Search',
    ],

];

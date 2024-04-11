<?php

return [

    'label' => 'Pagination navigation',

    'overview' => '{1} Προβολή 1 αποτελέσματος|[2,*] Προβολή :first έως :last (από :total αποτελέσματα)',

    'fields' => [

        'records_per_page' => [

            'label' => 'Ανά σελίδα',

            'options' => [
                'all' => 'Όλα',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Πρώτη σελίδα',
        ],

        'go_to_page' => [
            'label' => 'Πήγαινε στη σελίδα :page',
        ],

        'last' => [
            'label' => 'Τελευταία σελίδα',
        ],

        'next' => [
            'label' => 'Επόμενη',
        ],

        'previous' => [
            'label' => 'Προηγούμενη',
        ],

    ],

];

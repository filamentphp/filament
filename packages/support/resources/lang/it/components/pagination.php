<?php

return [

    'label' => 'Navigazione paginazione',

    'overview' => 'Mostrati da :first a :last di :total risultati',

    'fields' => [

        'records_per_page' => [

            'label' => 'per pagina',

            'options' => [
                'all' => 'Tutti',
            ],

        ],

    ],

    'actions' => [

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

];

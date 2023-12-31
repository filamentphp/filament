<?php

return [

    'label' => 'Paginação',

    'overview' => '{1} A mostrar 1 resultado|[2,*] A mostrar :first a :last de :total resultados',

    'fields' => [

        'records_per_page' => [

            'label' => 'Por página',

            'options' => [
                'all' => 'Todos',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Ir para a página :page',
        ],

        'next' => [
            'label' => 'Próximo',
        ],

        'previous' => [
            'label' => 'Anterior',
        ],

    ],

];

<?php

return [

    'label' => 'Paginering navigatie',

    'overview' => '{1} Toont 1 resultaat|[2,*] Toont :first tot :last van :total resultaten',

    'fields' => [

        'records_per_page' => [

            'label' => 'Per pagina',

            'options' => [
                'all' => 'Alles',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Ga naar pagina :page',
        ],

        'next' => [
            'label' => 'Volgende',
        ],

        'previous' => [
            'label' => 'Vorige',
        ],

    ],

];

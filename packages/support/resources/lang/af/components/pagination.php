<?php

return [

    'label' => 'Paginering navigasie',

    'overview' => '{1} Wys 1 resultaat|[2,*] Wys :first tot :last van :total resultate',

    'fields' => [

        'records_per_page' => [

            'label' => 'Per bladsy',

            'options' => [
                'all' => 'Almal',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Eerstens',
        ],

        'go_to_page' => [
            'label' => 'Gaan na bladsy :page',
        ],

        'last' => [
            'label' => 'Laaste',
        ],

        'next' => [
            'label' => 'Volgende',
        ],

        'previous' => [
            'label' => 'Vorige',
        ],

    ],

];

<?php

return [

    'label' => 'Paginazioaren nabigazioa',

    'overview' => '{1} Emaitza bat erakusten da|[2,*] :total emaitzatik :firstetik :lastera erakusten dira',

    'fields' => [

        'records_per_page' => [

            'label' => 'orriko',

            'options' => [
                'all' => 'Denak',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Joan :page orrira',
        ],

        'next' => [
            'label' => 'Hurrengoa',
        ],

        'previous' => [
            'label' => 'Aurrekoa',
        ],

    ],

];

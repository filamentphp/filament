<?php

return [

    'label' => 'Lapdales navigācija',

    'overview' => '{1} Rāda 1 rezultātu|[2,*] Rāda :first līdz :last no :total rezultātiem',

    'fields' => [

        'records_per_page' => [

            'label' => 'vienā lappusē',

            'options' => [
                'all' => 'Visi',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Doties uz lapu :page',
        ],

        'next' => [
            'label' => 'Nākamais',
        ],

        'previous' => [
            'label' => 'Iepriekšējais',
        ],

    ],

];

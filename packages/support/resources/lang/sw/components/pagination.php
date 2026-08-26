<?php

return [

    'label' => 'Urambazaji wa kurasa',

    'overview' => '{1} Inaonyesha kipengee 1|[2,*] Inaonyesha :first hadi :last kati ya vipengee :total',

    'fields' => [
        'records_per_page' => [
            'label' => 'kwa kurasa',

            'options' => [
                'all' => 'Zote',
            ],
        ],
    ],

    'actions' => [
        'first' => [
            'label' => 'Mwanzo',
        ],

        'go_to_page' => [
            'label' => 'Nenda kwenye kurasa :page',
        ],

        'last' => [
            'label' => 'Mwisho',
        ],

        'next' => [
            'label' => 'Mbele',
        ],

        'previous' => [
            'label' => 'Nyuma',
        ],
    ],

];

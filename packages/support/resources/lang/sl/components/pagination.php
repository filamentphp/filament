<?php

return [

    'label' => 'Navigacija',

    'overview' => '{1} Prikazan je 1 rezultat|[2,*] Prikazanih je :first do :last od skupno :total rezultatov',

    'fields' => [

        'records_per_page' => [

            'label' => 'Na stran',

            'options' => [
                'all' => 'Vse',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Prva',
        ],

        'go_to_page' => [
            'label' => 'Pojdi na stran :page',
        ],

        'last' => [
            'label' => 'Zadnja',
        ],

        'next' => [
            'label' => 'Naslednja',
        ],

        'previous' => [
            'label' => 'Prejšnja',
        ],

    ],

];

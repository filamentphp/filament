<?php

return [

    'label' => 'Stránkovanie',

    'overview' => '{1} Zobrazuje sa jeden výsledok|[2,*] Zobrazuje sa :first až :last z celkových :total výsledkov',

    'fields' => [

        'records_per_page' => [

            'label' => 'Počet na stranu',

            'options' => [
                'all' => 'Všetky',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Prvá',
        ],

        'go_to_page' => [
            'label' => 'Prejsť na stranu :page',
        ],

        'last' => [
            'label' => 'Posledná',
        ],

        'next' => [
            'label' => 'Ďalšia',
        ],

        'previous' => [
            'label' => 'Predošlá',
        ],

    ],

];

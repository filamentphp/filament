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

        'go_to_page' => [
            'label' => 'Prejsť na stranu :page',
        ],

        'next' => [
            'label' => 'Ďalšia',
        ],

        'previous' => [
            'label' => 'Predošlá',
        ],

    ],

];

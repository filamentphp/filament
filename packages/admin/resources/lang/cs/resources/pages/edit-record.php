<?php

return [

    'title' => 'Upravit :label',

    'breadcrumb' => 'Upravit',

    'actions' => [

        'delete' => [

            'label' => 'Smazat',

            'modal' => [

                'heading' => 'Smazat :label',

                'subheading' => 'Opravdu chcete akci provést?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Smazat',
                    ],

                ],

            ],

        ],

        'view' => [
            'label' => 'Zobrazit',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Zrušit',
            ],

            'save' => [
                'label' => 'Uložit',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Uloženo',
    ],

];

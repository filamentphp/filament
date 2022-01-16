<?php

return [

    'title' => 'Ändra :label',

    'breadcrumb' => 'Ändra',

    'actions' => [

        'delete' => [

            'label' => 'Ta bort',

            'modal' => [

                'heading' => 'Ta bort :label',

                'subheading' => 'Är du säker på att du vill göra det här?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Ta bort',
                    ],

                ],

            ],

        ],

        'view' => [
            'label' => 'Visa',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Avbryt',
            ],

            'save' => [
                'label' => 'Spara',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Sparad',
    ],

];

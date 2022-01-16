<?php

return [

    'title' => 'Bewerk :label',

    'breadcrumb' => 'Bewerken',

    'actions' => [

        'delete' => [

            'label' => 'Verwijderen',

            'modal' => [

                'heading' => 'Verwijder :label',

                'subheading' => 'Weet je zeker dat je dit wilt doen?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Verwijderen',
                    ],

                ],

            ],

        ],

        'view' => [
            'label' => 'Bekijken',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Annuleren',
            ],

            'save' => [
                'label' => 'Opslaan',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Opgeslagen',
    ],

];

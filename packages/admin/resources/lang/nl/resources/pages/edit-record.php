<?php

return [

    'title' => ':Label bewerken',

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

            'messages' => [
                'deleted' => 'Verwijderd',
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

<?php

return [

    'title' => 'Modifica :label',

    'breadcrumb' => 'Modifica',

    'actions' => [

        'delete' => [

            'label' => 'Elimina',

            'modal' => [

                'heading' => 'Elimina :label',

                'subheading' => 'Sei sicuro di volerlo fare?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Elimina',
                    ],

                ],

            ],

        ],

        'view' => [
            'label' => 'Guarda',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Annulla',
            ],

            'save' => [
                'label' => 'Salva',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Salvato',
    ],

];

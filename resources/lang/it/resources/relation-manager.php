<?php

return [

    'buttons' => [

        'attach' => [
            'label' => 'Collega esistente',
        ],

        'create' => [
            'label' => 'Nuovo',
        ],

        'detach' => [
            'label' => 'Scollega selezionati',
        ],

    ],

    'modals' => [

        'attach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annulla',
                ],

                'attach' => [
                    'label' => 'Collega',
                ],

                'attachAnother' => [
                    'label' => 'Collega & Collega un altro',
                ],

            ],

            'form' => [

                'related' => [
                    'placeholder' => 'Digita per cercare...',
                ],

            ],

            'heading' => 'Collega esistente',

            'messages' => [
                'attached' => 'Collegato!',
            ],

        ],

        'create' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annulla',
                ],

                'create' => [
                    'label' => 'Crea',
                ],

                'createAnother' => [
                    'label' => 'Crea & Crea un altro',
                ],

            ],

            'heading' => 'Crea',

            'messages' => [
                'created' => 'Creato!',
            ],

        ],

        'detach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annulla',
                ],

                'detach' => [
                    'label' => 'Scollega selezionati',
                ],

            ],

            'description' => 'Sei sicuro di voler scollegare i valori selezionati? Quest\'azione è irreversibile.',

            'heading' => 'Scollegare i valori selezionati?',

            'messages' => [
                'detached' => 'Scollegati!',
            ],

        ],

        'edit' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annulla',
                ],

                'save' => [
                    'label' => 'Salva',
                ],

            ],

            'heading' => 'Modifica',

            'messages' => [
                'saved' => 'Salvato!',
            ],

        ],

    ],

];

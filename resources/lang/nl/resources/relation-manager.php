<?php

return [

    'buttons' => [

        'attach' => [
            'label' => 'Koppel bestaande',
        ],

        'create' => [
            'label' => 'Nieuwe',
        ],

        'detach' => [
            'label' => 'Ontkoppel geselecteerde',
        ],

    ],

    'modals' => [

        'attach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annuleren',
                ],

                'attach' => [
                    'label' => 'Koppel',
                ],

                'attachAnother' => [
                    'label' => 'Koppel & koppel nog één',
                ],

            ],

            'form' => [

                'related' => [
                    'placeholder' => 'Start met typen om te zoeken...',
                ],

            ],

            'heading' => 'Koppel bestaande',

            'messages' => [
                'attached' => 'Gekoppeld!',
            ],

        ],

        'create' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annuleren',
                ],

                'create' => [
                    'label' => 'Aanmaken',
                ],

                'createAnother' => [
                    'label' => 'Opslaan & nog één maken',
                ],

            ],

            'heading' => 'Aanmaken',

            'messages' => [
                'created' => 'Aangemaakt!',
            ],

        ],

        'detach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annuleren',
                ],

                'detach' => [
                    'label' => 'Ontkoppel geselecteerde',
                ],

            ],

            'description' => 'Ben je zeker dat je de geselecteerde wilt ontkoppelen? Dit kan niet ongedaan gemaakt worden.',

            'heading' => 'Ontkoppel geselecteerde resultaten? ',

            'messages' => [
                'detached' => 'Ontkoppeld!',
            ],

        ],

        'edit' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Annuleren',
                ],

                'save' => [
                    'label' => 'Opslaan',
                ],

            ],

            'heading' => 'Bewerk',

            'messages' => [
                'saved' => 'Opgeslagen!',
            ],

        ],

    ],

];

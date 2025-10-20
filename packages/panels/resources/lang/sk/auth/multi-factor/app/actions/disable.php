<?php

return [

    'label' => 'Vypnúť',

    'modal' => [

        'heading' => 'Vypnúť overovaciu aplikáciu',

        'description' => 'Naozaj chcete prestať používať overovaciu aplikáciu? Vypnutím odstránite ďalšiu vrstvu zabezpečenia Vášho účtu.',

        'form' => [

            'code' => [

                'label' => 'Zadajte 6-miestny kód z overovacej aplikácie',

                'validation_attribute' => 'kód',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Namiesto toho použite obnovovací kód',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Zadaný kód je neplatný.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Alebo zadajte obnovovací kód',

                'validation_attribute' => 'obnovovací kód',

                'messages' => [

                    'invalid' => 'Zadaný obnovovací kód je neplatný.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Vypnúť overovaciu aplikáciu',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Overovacia aplikácia bola vypnutá',
        ],

    ],

];

<?php

return [

    'label' => 'Vypnúť',

    'modal' => [

        'heading' => 'Vypnúť e-mailové overovacie kódy',

        'description' => 'Naozaj chcete prestať dostávať e-mailové overovacie kódy? Vypnutím odstránite ďalšiu vrstvu zabezpečenia Vášho účtu.',

        'form' => [

            'code' => [

                'label' => 'Zadajte 6-miestny kód, ktorý sme Vám poslali e-mailom',

                'validation_attribute' => 'kód',

                'actions' => [

                    'resend' => [

                        'label' => 'Poslať nový kód e-mailom',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Poslali sme Vám nový kód e-mailom',
                            ],

                            'throttled' => [
                                'title' => 'Príliš veľa pokusov o opätovné odoslanie. Počkajte, prosím, pred ďalšou žiadosťou o kód.',
                            ],
                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Zadaný kód je neplatný.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Vypnúť e-mailové overovacie kódy',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'E-mailové overovacie kódy boli vypnuté',
        ],

    ],

];

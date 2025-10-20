<?php

return [

    'label' => 'Nastaviť',

    'modal' => [

        'heading' => 'Nastaviť e-mailové overovacie kódy',

        'description' => 'Pri každom prihlásení alebo vykonávaní citlivých akcií budete musieť zadať 6-miestny kód, ktorý Vám pošleme e-mailom. Skontrolujte svoj e-mail a zadajte 6-miestny kód na dokončenie nastavenia.',

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
                'label' => 'Povoliť e-mailové overovacie kódy',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'E-mailové overovacie kódy boli povolené',
        ],

    ],

];

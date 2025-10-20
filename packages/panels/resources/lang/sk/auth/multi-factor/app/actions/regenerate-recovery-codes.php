<?php

return [

    'label' => 'Obnoviť obnovovacie kódy',

    'modal' => [

        'heading' => 'Obnoviť obnovovacie kódy pre overovaciu aplikáciu',

        'description' => 'Ak stratíte svoje obnovovacie kódy, môžete ich tu obnoviť. Vaše staré obnovovacie kódy budú okamžite zneplatnené.',

        'form' => [

            'code' => [

                'label' => 'Zadajte 6-miestny kód z overovacej aplikácie',

                'validation_attribute' => 'kód',

                'messages' => [

                    'invalid' => 'Zadaný kód je neplatný.',

                ],

            ],

            'password' => [

                'label' => 'Alebo zadajte svoje aktuálne heslo',

                'validation_attribute' => 'heslo',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Obnoviť obnovovacie kódy',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Boli vygenerované nové obnovovacie kódy pre overovaciu aplikáciu',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nové obnovovacie kódy',

            'description' => 'Uložte si nasledujúce obnovovacie kódy na bezpečné miesto. Budú zobrazené iba raz, ale budete ich potrebovať, ak stratíte prístup k svojej overovacej aplikácii:',

            'actions' => [

                'submit' => [
                    'label' => 'Zavrieť',
                ],

            ],

        ],

    ],

];

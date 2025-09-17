<?php

return [

    'label' => 'Obnovit obnovovací kódy',

    'modal' => [

        'heading' => 'Obnovit obnovovací kódy pro ověřovací aplikaci',

        'description' => 'Pokud ztratíte své obnovovací kódy, můžete je zde obnovit. Vaše staré obnovovací kódy budou okamžitě zneplatněny.',

        'form' => [

            'code' => [

                'label' => 'Zadejte 6-místný kód z ověřovací aplikace',

                'validation_attribute' => 'kód',

                'messages' => [

                    'invalid' => 'Zadaný kód je neplatný.',

                ],

            ],

            'password' => [

                'label' => 'Nebo zadejte své aktuální heslo',

                'validation_attribute' => 'heslo',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Obnovit obnovovací kódy',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Byly vygenerovány nové obnovovací kódy pro ověřovací aplikaci',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nové obnovovací kódy',

            'description' => 'Uložte si prosím následující obnovovací kódy na bezpečné místo. Budou zobrazeny pouze jednou, ale budete je potřebovat, pokud ztratíte přístup ke své ověřovací aplikaci:',

            'actions' => [

                'submit' => [
                    'label' => 'Zavřít',
                ],

            ],

        ],

    ],

];

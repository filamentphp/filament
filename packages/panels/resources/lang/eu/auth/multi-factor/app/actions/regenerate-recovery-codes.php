<?php

return [

    'label' => 'Berreskuratze-kodeak birsortu',

    'modal' => [

        'heading' => 'Autentifikazio-aplikazioaren berreskuratze-kodeak birsortu',

        'description' => 'Berreskuratze-kodeak galtzen badituzu, hemen birsor ditzakezu. Zure berreskuratze-kode zaharrak berehala baliogabetuko dira.',

        'form' => [

            'code' => [

                'label' => 'Sartu autentifikazio-aplikazioko 6 digituko kodea',

                'validation_attribute' => 'kodea',

                'messages' => [

                    'invalid' => 'Sartu duzun kodea ez da baliozkoa.',

                    'rate_limited' => 'Saiakera gehiegi. Mesedez, saiatu berriro geroago.',

                ],

            ],

            'password' => [

                'label' => 'Edo, sartu zure uneko pasahitza',

                'validation_attribute' => 'pasahitza',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Berreskuratze-kodeak birsortu',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Autentifikazio-aplikazioaren berreskuratze-kode berriak sortu dira',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Berreskuratze-kode berriak',

            'description' => 'Mesedez, gorde ondorengo berreskuratze-kodeak leku seguru batean. Behin bakarrik erakutsiko dira, baina behar izango dituzu autentifikazio-aplikaziorako sarbidea galtzen baduzu:',

            'actions' => [

                'submit' => [
                    'label' => 'Itxi',
                ],

            ],

        ],

    ],

];

<?php

return [

    'label' => 'Rekreirаj kodovi za obnovуvanje',

    'modal' => [

        'heading' => 'Rekreirаj kodovi za obnovуvanje na aplikаcijata za avtentifikacija',

        'description' => 'Ako gi izgubite vašite kodovi za obnovуvanje, možete da gi rekreirаte ovde. Vašite stari kodovi za obnovуvanje vednaš ќe stanat nevalidni.',

        'form' => [

            'code' => [

                'label' => 'Vnesete go 6-cifreniot kod od aplikаcijata za avtentifikacija',

                'validation_attribute' => 'kod',

                'messages' => [

                    'invalid' => 'Kodot što go vnesovte ne e validen.',

                ],

            ],

            'password' => [

                'label' => 'Ili, vnesete ja vašata tekovna lozinka',

                'validation_attribute' => 'lozinka',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Rekreirаj kodovi za obnovуvanje',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Novi kodovi za obnovуvanje na aplikаcijata za avtentifikacija se kreirаni',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Novi kodovi za obnovуvanje',

            'description' => 'Ve molime začuvајte gi slednite kodovi za obnovуvanje na bezbedno mesto. Tie ќe bidat prikažаni samo ednаš, no ќe vi trebаat ako izgubite pristаp do vašata aplikаcija za avtentifikacija:',

            'actions' => [

                'submit' => [
                    'label' => 'Zatvori',
                ],

            ],

        ],

    ],

];

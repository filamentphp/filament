<?php

return [

    'label' => 'Luo palautuskoodit',

    'modal' => [

        'heading' => 'Luo uudet todennussovelluksen palautuskoodit',

        'description' => 'Jos kadotat palautuskoodit, voit luoda uudet täällä. Vanhat palautuskoodit eivät toimi enää tämän jälkeen.',

        'form' => [

            'code' => [

                'label' => 'Syötä todennussovelluksen antama koodi',

                'validation_attribute' => 'koodi',

                'messages' => [

                    'invalid' => 'Annettu koodi on väärin.',

                ],

            ],

            'password' => [

                'label' => 'Tai anna nykyinen salasana',

                'validation_attribute' => 'salasana',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Luo uudet palautuskoodit',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Uudet todennussovelluksen palautuskoodit on luotu',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Uudet palautuskoodit',

            'description' => 'Tallenna seuraavat palautuskoodit turvalliseen paikkaan. Ne näytetään vain nyt tämän kerran ja tarvitset näitä jos menetät pääsyn todennussovellukseen:',

            'actions' => [

                'submit' => [
                    'label' => 'Sulje',
                ],

            ],

        ],

    ],

];

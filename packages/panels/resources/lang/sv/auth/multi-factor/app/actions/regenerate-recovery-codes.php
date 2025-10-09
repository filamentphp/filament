<?php

return [

    'label' => 'Återskapa återställningskoder',

    'modal' => [

        'heading' => 'Återskapa återställningskoder för autentiseringsapp',

        'description' => 'Om du förlorar dina återställningskoder kan du återskapa dem här. Dina gamla återställningskoder kommer att ogiltigförklaras omedelbart.',

        'form' => [

            'code' => [

                'label' => 'Ange den 6-siffriga koden från autentiseringsappen',

                'validation_attribute' => 'kod',

                'messages' => [

                    'invalid' => 'Koden du angav är ogiltig.',

                ],

            ],

            'password' => [

                'label' => 'Eller, ange ditt nuvarande lösenord',

                'validation_attribute' => 'lösenord',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Återskapa återställningskoder',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Nya återställningskoder för autentiseringsapp har genererats',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nya återställningskoder',

            'description' => 'Spara följande återställningskoder på en säker plats. De visas endast en gång, men du behöver dem om du förlorar tillgången till din autentiseringsapp:',

            'actions' => [

                'submit' => [
                    'label' => 'Stäng',
                ],

            ],

        ],

    ],

];

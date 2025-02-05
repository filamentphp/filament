<?php

return [

    'single' => [

        'label' => 'Tühista seos',

        'modal' => [

            'heading' => 'Tühista seos :label',

            'actions' => [

                'dissociate' => [
                    'label' => 'Tühista seos',
                ],

            ],

        ],

        'notifications' => [

            'dissociated' => [
                'title' => 'Seos tühistatud',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Tühista valitud seosed',

        'modal' => [

            'heading' => 'Tühista valitud :label seosed',

            'actions' => [

                'dissociate' => [
                    'label' => 'Tühista seosed',
                ],

            ],

        ],

        'notifications' => [

            'dissociated' => [
                'title' => 'Seosed tühistatud',
            ],

        ],

    ],

];

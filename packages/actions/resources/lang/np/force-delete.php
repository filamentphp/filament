<?php

return [

    'single' => [

        'label' => 'जबर्जस्ती मेटाउनुहोस्',

        'modal' => [

            'heading' => ':label जबर्जस्ती मेटाउनुहोस्',

            'actions' => [

                'delete' => [
                    'label' => 'मेटाउनुहोस्',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'मेटाइयो',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Force delete selected',

        'modal' => [

            'heading' => 'Force delete selected :label',

            'actions' => [

                'delete' => [
                    'label' => 'Delete',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Deleted',
            ],

        ],

    ],

];

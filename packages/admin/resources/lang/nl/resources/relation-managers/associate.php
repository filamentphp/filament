<?php

return [

    'action' => [

        'label' => 'Koppelen',

        'modal' => [

            'heading' => 'Koppel :label',

            'fields' => [

                'record_ids' => [
                    'label' => 'Records',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => 'Koppelen',
                ],

                'associate_and_associate_another' => [
                    'label' => 'Koppelen & nieuwe koppelen',
                ],

            ],

        ],

        'messages' => [
            'associated' => 'Gekoppeld',
        ],

    ],

];

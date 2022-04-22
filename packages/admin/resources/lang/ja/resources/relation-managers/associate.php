<?php

return [

    'action' => [

        'label' => '紐づける',

        'modal' => [

            'heading' => ':labelを紐づける',

            'fields' => [

                'record_ids' => [
                    'label' => 'レコード',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => '紐づける',
                ],

                'associate_and_associate_another' => [
                    'label' => '保存して、続けて紐づける',
                ],

            ],

        ],

        'messages' => [
            'associated' => '紐づけました',
        ],

    ],

];

<?php

return [

    'single' => [

        'label' => '解除',

        'modal' => [

            'heading' => ':labelの解除',

            'actions' => [

                'dissociate' => [
                    'label' => '解除',
                ],

            ],

        ],

        'messages' => [
            'dissociated' => '解除しました',
        ],

    ],

    'multiple' => [

        'label' => '解除対象の選択',

        'modal' => [

            'heading' => '選択した:labelを解除',

            'actions' => [

                'dissociate' => [
                    'label' => '選択先の解除',
                ],

            ],

        ],

        'messages' => [
            'dissociated' => '解除されました',
        ],

    ],

];

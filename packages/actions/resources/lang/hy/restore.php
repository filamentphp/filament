<?php

return [

    'single' => [

        'label' => 'Վերականգնել',

        'modal' => [

            'heading' => 'Վերականգնել :label',

            'actions' => [

                'restore' => [
                    'label' => 'Վերականգնել',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Վերականգնված է',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Վերականգնել ընտրվածները',

        'modal' => [

            'heading' => 'Վերականգնել ընտրված :label',

            'actions' => [

                'restore' => [
                    'label' => 'Վերականգնել',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Վերականգնված է',
            ],

            'restored_partial' => [
                'title' => 'Վերականգնվեց :count-ը ընդհանուր :total-ից',
                'missing_authorization_failure_message' => 'Դուք իրավունք չունեք վերականգնելու :count-ը։',
                'missing_processing_failure_message' => ':count-ը հնարավոր չեղավ վերականգնել։',
            ],

            'restored_none' => [
                'title' => 'Չհաջողվեց վերականգնել',
                'missing_authorization_failure_message' => 'Դուք իրավունք չունեք վերականգնելու :count-ը։',
                'missing_processing_failure_message' => ':count-ը հնարավոր չեղավ վերականգնել։',
            ],

        ],

    ],

];

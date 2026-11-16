<?php

return [

    'single' => [

        'label' => 'Ջնջել',

        'modal' => [

            'heading' => 'Ջնջել :label',

            'actions' => [

                'delete' => [
                    'label' => 'Ջնջել',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Ջնջված է',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Ջնջել ընտրվածները',

        'modal' => [

            'heading' => 'Ջնջել ընտրված :label',

            'actions' => [

                'delete' => [
                    'label' => 'Ջնջել',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Ջնջված է',
            ],

            'deleted_partial' => [
                'title' => 'Ջնջվեց :count-ը ընդհանուր :total-ից',
                'missing_authorization_failure_message' => 'Դուք իրավունք չունեք ջնջելու :count-ը։',
                'missing_processing_failure_message' => ':count-ը հնարավոր չեղավ ջնջել։',
            ],

            'deleted_none' => [
                'title' => 'Չհաջողվեց ջնջել',
                'missing_authorization_failure_message' => 'Դուք իրավունք չունեք ջնջելու :count-ը։',
                'missing_processing_failure_message' => ':count-ը հնարավոր չեղավ ջնջել։',
            ],

        ],

    ],

];

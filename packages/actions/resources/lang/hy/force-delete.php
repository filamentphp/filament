<?php

return [

    'single' => [

        'label' => 'Սպառիչ ջնջում',

        'modal' => [

            'heading' => 'Սպառիչ ջնջում :label',

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

        'label' => 'Սպառիչ ջնջել ընտրածը',

        'modal' => [

            'heading' => 'Սպառիչ ջնջել ընտրած :label',

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
                'missing_processing_failure_message' => ':count-ը հնարավոր չէր ջնջել։',
            ],

            'deleted_none' => [
                'title' => 'Չհաջողվեց ջնջել',
                'missing_authorization_failure_message' => 'Դուք իրավունք չունեք ջնջելու :count-ը։',
                'missing_processing_failure_message' => ':count-ը հնարավոր չէր ջնջել։',
            ],

        ],

    ],

];

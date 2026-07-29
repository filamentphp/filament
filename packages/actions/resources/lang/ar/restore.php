<?php

return [

    'single' => [

        'label' => 'استعادة',

        'modal' => [

            'heading' => 'استعادة :label',

            'actions' => [

                'restore' => [
                    'label' => 'استعادة',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'تمت الاستعادة',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'استعادة المحدد',

        'modal' => [

            'heading' => 'استعادة :label',

            'actions' => [

                'restore' => [
                    'label' => 'استعادة',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'تمت الاستعادة',
            ],

            'restored_partial' => [
                'title' => '{1} تمت استعادة سجل واحد من أصل :total|{2} تمت استعادة سجلين من أصل :total|[3,10] تمت استعادة :count سجلات من أصل :total|[11,*] تمت استعادة :count سجل من أصل :total',
                'missing_authorization_failure_message' => '{1} ليس لديك صلاحية لاستعادة سجل واحد.|{2} ليس لديك صلاحية لاستعادة سجلين.|[3,10] ليس لديك صلاحية لاستعادة :count سجلات.|[11,*] ليس لديك صلاحية لاستعادة :count سجل.',
                'missing_processing_failure_message' => '{1} تعذر استعادة سجل واحد.|{2} تعذر استعادة سجلين.|[3,10] تعذر استعادة :count سجلات.|[11,*] تعذر استعادة :count سجل.',
            ],

            'restored_none' => [
                'title' => 'فشل في الاستعادة',
                'missing_authorization_failure_message' => '{1} ليس لديك صلاحية لاستعادة سجل واحد.|{2} ليس لديك صلاحية لاستعادة سجلين.|[3,10] ليس لديك صلاحية لاستعادة :count سجلات.|[11,*] ليس لديك صلاحية لاستعادة :count سجل.',
                'missing_processing_failure_message' => '{1} تعذر استعادة سجل واحد.|{2} تعذر استعادة سجلين.|[3,10] تعذر استعادة :count سجلات.|[11,*] تعذر استعادة :count سجل.',
            ],

        ],

    ],

];

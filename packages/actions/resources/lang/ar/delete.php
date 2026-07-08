<?php

return [

    'single' => [

        'label' => 'حذف',

        'modal' => [

            'heading' => 'حذف :label',

            'actions' => [

                'delete' => [
                    'label' => 'حذف',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'تم الحذف',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'حذف المحدد',

        'modal' => [

            'heading' => 'حذف المحدد :label',

            'actions' => [

                'delete' => [
                    'label' => 'حذف',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'تم الحذف',
            ],

            'deleted_partial' => [
                'title' => '{1} تم حذف سجل واحد من :total|{2} تم حذف سجلين من :total|[3,10] تم حذف :count سجلات من :total|[11,*] تم حذف :count سجل من :total',
                'missing_authorization_failure_message' => '{1} ليس لديك إذن لحذف سجل واحد.|{2} ليس لديك إذن لحذف سجلين.|[3,10] ليس لديك إذن لحذف :count سجلات.|[11,*] ليس لديك إذن لحذف :count سجل.',
                'missing_processing_failure_message' => '{1} تعذر حذف سجل واحد.|{2} تعذر حذف سجلين.|[3,10] تعذر حذف :count سجلات.|[11,*] تعذر حذف :count سجل.',
            ],

            'deleted_none' => [
                'title' => 'لم يتم حذف أي شيء',
                'missing_authorization_failure_message' => '{1} ليس لديك إذن لحذف سجل واحد.|{2} ليس لديك إذن لحذف سجلين.|[3,10] ليس لديك إذن لحذف :count سجلات.|[11,*] ليس لديك إذن لحذف :count سجل.',
                'missing_processing_failure_message' => '{1} لم يتم حذف سجل واحد.|{2} لم يتم حذف سجلين.|[3,10] لم يتم حذف :count سجلات.|[11,*] لم يتم حذف :count سجل.',
            ],

        ],

    ],

];

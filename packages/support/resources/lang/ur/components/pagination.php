<?php

return [

    'label' => 'صفحہ بندی',

    'overview' => '{1} 1 نتیجہ دکھایا جا رہا ہے|[2,*] :first سے :last تک کے :total نتائج دکھا رہے ہیں',

    'fields' => [

        'records_per_page' => [

            'label' => 'فی صفحہ',

            'options' => [
                'all' => 'سب',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'پہلا',
        ],

        'go_to_page' => [
            'label' => 'صفحہ :page پر جائیں',
        ],

        'last' => [
            'label' => 'آخری',
        ],

        'next' => [
            'label' => 'اگلا',
        ],

        'previous' => [
            'label' => 'پچھلا',
        ],

    ],

];

<?php

return [

    'label' => 'التنقل بين الصفحات',

    'overview' => '{1} عرض نتيجة واحدة|[3,10] عرض :first إلى :last من :total نتائج|[2,*] عرض :first إلى :last من :total نتيجة',

    'fields' => [

        'records_per_page' => [

            'label' => 'لكل صفحة',

            'options' => [
                'all' => 'الكل',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'الأولي',
        ],

        'go_to_page' => [
            'label' => 'انتقل إلى صفحة :page',
        ],

        'last' => [
            'label' => 'الأخيرة',
        ],

        'next' => [
            'label' => 'التالي',
        ],

        'previous' => [
            'label' => 'السابق',
        ],

    ],

];

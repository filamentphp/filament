<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'بحث',
            'placeholder' => 'بحث',
        ],

    ],

    'pagination' => [

        'label' => 'التنقل بين الصفحات',

        'overview' => 'عرض :first إلي :last من :total النتائج',

        'fields' => [

            'records_per_page' => [
                'label' => 'لكل صفحة',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'انتقل إلى صفحة :page',
            ],

            'next' => [
                'label' => 'التالي',
            ],

            'previous' => [
                'label' => 'السابق',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'تصفية',
        ],

        'open_actions' => [
            'label' => 'Open actions',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Are you sure you would like to do this?',

            'buttons' => [

                'cancel' => [
                    'label' => 'إلغاء',
                ],

                'confirm' => [
                    'label' => 'Confirm',
                ],

                'submit' => [
                    'label' => 'Submit',
                ],

            ],

        ],

        'buttons' => [

            'select_all' => [
                'label' => 'Select all :count records',
            ],

        ],

    ],

    'empty' => [
        'heading' => 'لا توجد سجلات',
    ],

];

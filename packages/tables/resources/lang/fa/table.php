<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'جستجو',
            'placeholder' => 'جستجو',
        ],

    ],

    'pagination' => [

        'label' => 'جهت یابی صفحه بندی',

        'overview' => 'در حال نمایش :first به :last از :total نتایج',

        'fields' => [

            'records_per_page' => [
                'label' => 'در هر صفحه',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'برو به صفحه :page',
            ],

            'next' => [
                'label' => 'بعدی',
            ],

            'previous' => [
                'label' => 'قبلی',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'فلتر',
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
                    'label' => 'لغو',
                ],

                'confirm' => [
                    'label' => 'Confirm',
                ],

                'submit' => [
                    'label' => 'Submit',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'هیچ ریکارد دریافت نشد.',
    ],

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => 'Select all :count',
            ],

        ],

    ],

];

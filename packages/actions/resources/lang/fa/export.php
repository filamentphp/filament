<?php

return [

    'label' => 'اکسپورت :label',

    'modal' => [

        'heading' => 'اکسپورت :label',

        'form' => [

            'columns' => [

                'label' => 'ستون ها',

                'actions' => [

                    'select_all' => [
                        'label' => 'انتخاب همه',
                    ],

                    'deselect_all' => [
                        'label' => 'لغو انتخاب همه',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':ستون فعال است',
                    ],

                    'label' => [
                        'label' => ':برچسب ستونی',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'اکسپورت',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'اکسپورت انجام شد',

            'actions' => [

                'download_csv' => [
                    'label' => 'دانلود csv.',
                ],

                'download_xlsx' => [
                    'label' => 'دانلود xlsx.',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'اکسپورت خیلی بزرگ است',
            'body' => 'شما نمی توانید بیش از 1 ردیف را به طور همزمان صادر کنید.|شما نمی توانید بیش از :count ردیف را به طور همزمان صادر کنید.',
        ],

        'no_columns' => [
            'title' => 'ستونی انتخاب نشده است',
            'body' => 'لطفاً حداقل یک ستون را برای خروجی گرفتن انتخاب کنید.',
        ],

        'started' => [
            'title' => 'اکسپورت آغاز شد',
            'body' => 'اکسپورت شما آغاز شده است و 1 ردیف در پس‌زمینه پردازش می‌شود.|اکسپورت شما آغاز شده است و :count ردیف در پس‌زمینه پردازش می‌شوند.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

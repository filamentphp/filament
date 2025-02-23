<?php

return [

    'label' => ':labelni export qilish',

    'modal' => [

        'heading' => ':labelni export qilish',

        'form' => [

            'columns' => [

                'label' => 'Ustunlar',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column yoqilgan',
                    ],

                    'label' => [
                        'label' => ':column belgi',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Export',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Export yakunlandi',

            'actions' => [

                'download_csv' => [
                    'label' => 'Yuklab olish .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Yuklab olish .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Export qilish uchun juda katta',
            'body' => 'Bir vaqtning o\'zida 1 tadan ortiq qatorni eksport qila olmaysiz.|Bir vaqtning o\'zida :count satrdan ortiq eksport qila olmaysiz.',
        ],

        'started' => [
            'title' => 'Export boshlandi',
            'body' => 'Eksportingiz boshlandi va 1 qator fonda qayta ishlanadi. Tugallangach, yuklab olish havolasi bilan bildirishnoma olasiz.|Eksportingiz boshlandi va :count qatorlari fonda qayta ishlanadi. Yuklab olish tugallangandan so\'ng siz yuklab olish havolasi bilan bildirishnoma olasiz.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

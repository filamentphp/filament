<?php

return [

    'label' => 'İdxal: :label',

    'modal' => [

        'heading' => 'İdxal: :label',

        'form' => [

            'file' => [
                'label' => 'Fayl',
                'placeholder' => 'Bir CSV faylı seçin',
            ],

            'columns' => [
                'label' => 'Sütunlar',
                'placeholder' => 'Sütunları uyğunlaşdırın',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Nümunə CSV faylını endir',
            ],

            'import' => [
                'label' => 'İdxal',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'İdxal tamamlandı',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Uğursuz sətir haqqında məlumatları endir|Uğursuz sətirlər haqqında məlumatları endir',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Seçilən fayl çox böyükdür',
            'body' => 'Bir dəfədə 1-dən çox sətiri olan faylı idxal edə bilməzsiniz.|Bir dəfədə :count dən çox sətiri olan faylı idxal edə bilməzsiniz.',
        ],

        'started' => [
            'title' => 'İdxal başladı',
            'body' => 'İdxal başladı və 1 sətir arxa planda işlənəcək.|İdxal başladı və :count sətir arxa planda işlənəcək',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'xəta',
        'system_error' => 'Sistem xətası, texniki dəstək ilə əlaqə saxlayın',
    ],

];

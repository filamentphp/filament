<?php

return [

    'label' => 'Dışa Aktar :label',

    'modal' => [

        'heading' => 'Dışa Aktar :label',

        'form' => [

            'columns' => [

                'label' => 'Sütunlar',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column etkin',
                    ],

                    'label' => [
                        'label' => ':column etiketi',
                    ],

                ],

                'actions' => [

                    'select_all' => [
                        'label' => 'Tümünü seç',
                    ],

                    'deselect_all' => [
                        'label' => 'Tüm seçimleri kaldır',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Dışa Aktar',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Dışa Aktarım Tamamlandı',

            'actions' => [

                'download_csv' => [
                    'label' => '.csv Olarak İndir',
                ],

                'download_xlsx' => [
                    'label' => '.xlsx Olarak İndir',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Maksimum Satır Sayısı Aşıldı',
            'body' => 'Birden fazla satırı dışa aktaramazsınız.|:count satırı dışa aktaramazsınız.',
        ],

        'no_columns' => [
            'title' => 'Hiçbir sütun seçilmedi',
            'body' => 'Lütfen dışa aktarmak için en az bir sütun seçin.',
        ],

        'started' => [
            'title' => 'Dışa Aktarım Başladı',
            'body' => 'Dışa aktarım başladı ve 1 satır arka planda işlenecek.|Dışa aktarım başladı ve :count satır arka planda işlenecek.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

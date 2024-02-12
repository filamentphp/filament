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

        'started' => [
            'title' => 'Dışa Aktarım Başladı',
            'body' => 'Dışa aktarım başladı ve 1 satır arka planda işlenecek.|Dışa aktarım başladı ve :count satır arka planda işlenecek.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];

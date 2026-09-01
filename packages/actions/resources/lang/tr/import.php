<?php

return [

    'label' => 'İçe Aktar: :label',

    'modal' => [

        'heading' => 'İçe Aktar: :label',

        'form' => [

            'file' => [
                'label' => 'Dosya',
                'placeholder' => 'Bir CSV dosyası seçin',
                'rules' => [
                    'duplicate_columns' => '{0} Dosya birden fazla boş sütun başlığı içeremez.|{1,*} Dosya tekrar eden sütun başlığı içeremez: :columns.',
                ],
            ],

            'columns' => [
                'label' => 'Sütunlar',
                'placeholder' => 'Sütunları eşleştirin',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Örnek CSV Dosyasını İndir',
            ],

            'import' => [
                'label' => 'İçe Aktar',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'İçe Aktarım Tamamlandı',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Başarısız satır hakkında bilgileri indir|Başarısız satırlar hakkında bilgileri indir',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Yüklenen Dosya Çok Büyük',
            'body' => 'Aynı anda 1\'den fazla satır içeren dosyaları içe aktaramazsınız.|Aynı anda :count\'den fazla satır içeren dosyaları içe aktaramazsınız.',
        ],

        'started' => [
            'title' => 'İçe Aktarım Başladı',
            'body' => 'İçe aktarım başladı ve 1 satır arka planda işlenecek.|İçe aktarım başladı ve :count satır arka planda işlenecek.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'Sistem Hatası',
        'column_mapping_required_for_new_record' => ':attribute sütunu dosyadaki bir sütun ile eşleştirilmedi, fakat bu sütun yeni kayıt oluşturmak için gerekli bir sütun.',
    ],

];

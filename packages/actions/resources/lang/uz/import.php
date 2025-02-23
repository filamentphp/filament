<?php

return [

    'label' => ':labelni import qilish',

    'modal' => [

        'heading' => ':labelni import qilish',

        'form' => [

            'file' => [

                'label' => 'Fayl',

                'placeholder' => 'CSV faylini yuklang',

                'rules' => [
                    'duplicate_columns' => '{0} Faylda bittadan ortiq boʻsh ustun sarlavhasi boʻlmasligi kerak.|{1,*} Faylda ikki nusxadagi ustun sarlavhalari boʻlmasligi kerak: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Ustunlar',
                'placeholder' => 'Ustunni tanlang',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Na\'muna CSV faylni yuklab oling',
            ],

            'import' => [
                'label' => 'Import',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import yakunlandi',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Muvaffaqiyatsiz satr haqida ma\'lumotni yuklab oling|Muvaffaqiyatsiz qatorlar haqida ma\'lumotni yuklab oling',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Yuklangan CSV fayl juda katta',
            'body' => 'Bir vaqtning o\'zida 1 tadan ortiq qatorni import qila olmaysiz.|Bir vaqtning o\'zida :count dan ortiq qatorni import qila olmaysiz.',
        ],

        'started' => [
            'title' => 'Import boshlandi',
            'body' => 'Importingiz boshlandi va 1 qator fonda qayta ishlanadi.|Importingiz boshlandi va :count qatorlari fonda qayta ishlanadi.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'xatolik',
        'system_error' => 'Tizim xatosi, yordam xizmatiga murojaat qiling.',
        'column_mapping_required_for_new_record' => ':atribut ustuni fayldagi ustunga moslashtirilmagan, lekin u yangi yozuvlar yaratish uchun talab qilinadi.',
    ],

];

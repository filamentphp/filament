<?php

return [

    'column_toggle' => [

        'heading' => 'Sütunlar',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 've :count daha',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Toplu işlemler için tüm öğeleri seç/seçimi kaldır.',
        ],

        'bulk_select_record' => [
            'label' => 'Toplu işlemler için :key öğesini seç/seçimi kaldır.',
        ],

        'bulk_select_group' => [
            'label' => 'Toplu işlemler için :title grubunu seç/seçimi kaldır.',
        ],

        'search' => [
            'label' => 'Ara',
            'placeholder' => 'Ara',
            'indicator' => 'Ara',
        ],

    ],

    'summary' => [

        'heading' => 'Özet',

        'subheadings' => [
            'all' => 'Tüm :label',
            'group' => ':group özeti',
            'page' => 'Bu sayfa',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Ortalama',
            ],

            'count' => [
                'label' => 'Sayı',
            ],

            'sum' => [
                'label' => 'Toplam',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Sıralamayı bitir',
        ],

        'enable_reordering' => [
            'label' => 'Kayıtları sırala',
        ],

        'filter' => [
            'label' => 'Filtrele',
        ],

        'group' => [
            'label' => 'Grupla',
        ],

        'open_bulk_actions' => [
            'label' => 'Toplu işlemler',
        ],

        'toggle_columns' => [
            'label' => 'Sütunları göster/gizle',
        ],

    ],

    'empty' => [

        'heading' => ':model Yok',

        'description' => 'Başlamak için bir :model oluşturun.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Filtreyi kaldır',
            ],

            'remove_all' => [
                'label' => 'Tüm filtreleri kaldır',
                'tooltip' => 'Tüm filtreleri kaldır',
            ],

            'reset' => [
                'label' => 'Sıfırla',
            ],

        ],

        'heading' => 'Filtreler',

        'indicator' => 'Aktif filtreler',

        'multi_select' => [
            'placeholder' => 'Tümü',
        ],

        'select' => [
            'placeholder' => 'Tümü',
        ],

        'trashed' => [

            'label' => 'Silinmiş kayıtlar',

            'only_trashed' => 'Yalnızca silinmiş kayıtlar',

            'with_trashed' => 'Silinmiş kayıtlarla birlikte',

            'without_trashed' => 'Silinmiş kayıtlar olmadan',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupla',
                'placeholder' => 'Grupla',
            ],

            'direction' => [

                'label' => 'Grup yönü',

                'options' => [
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Kayıtları sıralamak için sürükleyip bırakın.',

    'selection_indicator' => [

        'selected_count' => '1 kayıt seçildi|:count kayıt seçildi',

        'actions' => [

            'select_all' => [
                'label' => 'Tüm :count kaydı seç ',
            ],

            'deselect_all' => [
                'label' => 'Tüm seçimleri kaldır',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Şuna göre sırala',
            ],

            'direction' => [

                'label' => 'Sıralama yönü',

                'options' => [
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ],

            ],

        ],

    ],

];

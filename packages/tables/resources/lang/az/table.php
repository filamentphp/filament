<?php

return [

    'column_manager' => [

        'heading' => 'Sütunlar',

    ],

    'columns' => [

        'actions' => [
            'label' => 'Əməliyyat|Əməliyyatlar',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => ':count az göstər',
                'expand_list' => ':count daha çox göstər',
            ],

            'more_list_items' => 'və :count daha',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Çoxlu hərəkətlər üçün bütün elementləri seç/seçimi yığışdır.',
        ],

        'bulk_select_record' => [
            'label' => 'Çoxlu hərəkətlər üçün :key elementini seç/seçimi yığışdır.',
        ],

        'bulk_select_group' => [
            'label' => 'Çoxlu hərəkətlər üçün :title qrupunu seç/seçimi yığışdır.',
        ],

        'search' => [
            'label' => 'Axtar',
            'placeholder' => 'Axtar',
            'indicator' => 'Axtar',
        ],

    ],

    'summary' => [

        'heading' => 'Xülasə',

        'subheadings' => [
            'all' => 'Bütün :label',
            'group' => ':group xülasəsi',
            'page' => 'Bu səhifə',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Ortalama',
            ],

            'count' => [
                'label' => 'Say',
            ],

            'sum' => [
                'label' => 'Toplam',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Sıralamanı yekunlaşdır',
        ],

        'enable_reordering' => [
            'label' => 'Sıralamanı başlat',
        ],

        'filter' => [
            'label' => 'Filtrlə',
        ],

        'group' => [
            'label' => 'Qrupla',
        ],

        'open_bulk_actions' => [
            'label' => 'Çoxlu hərəkətlər',
        ],

        'column_manager' => [
            'label' => 'Sütunları göstər/gizlət',
        ],

    ],

    'empty' => [

        'heading' => ':model Yoxdur',

        'description' => 'Başlamaq üçün bir :model yaradın.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Filtrləri tətbiq et',
            ],

            'remove' => [
                'label' => 'Filtri yığışdır',
            ],

            'remove_all' => [
                'label' => 'Bütün filtrləri yığışdır',
                'tooltip' => 'Bütün filtrləri yığışdır',
            ],

            'reset' => [
                'label' => 'Sıfırla',
            ],

        ],

        'heading' => 'Filtrlər',

        'indicator' => 'Aktiv filtrlər',

        'multi_select' => [
            'placeholder' => 'Hamısı',
        ],

        'select' => [
            'placeholder' => 'Hamısı',
        ],

        'trashed' => [

            'label' => 'Silinmiş məlumatlar',

            'only_trashed' => 'Sadəcə silinmiş məlumatlar',

            'with_trashed' => 'Silinmiş məlumatlarla birlikdə',

            'without_trashed' => 'Silinmiş məlumatlar olmadan',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Qrupla',
                'placeholder' => 'Qrupla',
            ],

            'direction' => [

                'label' => 'Qrup istiqaməti',

                'options' => [
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Məlumatları sıralamaq üçün sürüşdürüb buraxın.',

    'selection_indicator' => [

        'selected_count' => '1 məlumat seçildi|:count məlumat seçildi',

        'actions' => [

            'select_all' => [
                'label' => 'Bütün :count məlumatı seç',
            ],

            'deselect_all' => [
                'label' => 'Bütün seçimləri yığışdır',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Buna görə sırala',
            ],

            'direction' => [

                'label' => 'Sıralama istiqaməti',

                'options' => [
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ],

            ],

        ],

    ],

];

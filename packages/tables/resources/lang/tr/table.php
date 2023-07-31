<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 've :count daha',
        ],

    ],

    'fields' => [

        'search' => [
            'label' => 'Ara',
            'placeholder' => 'Ara',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Sıralamayı kapat',
        ],

        'enable_reordering' => [
            'label' => 'Sıralamayı aç',
        ],

        'filter' => [
            'label' => 'Filtrele',
        ],

        'open_bulk_actions' => [
            'label' => 'Eylemleri aç',
        ],

        'toggle_columns' => [
            'label' => 'Sütunları göster/gizle',
        ],

    ],

    'empty' => [
        'heading' => 'Kayıt bulunamadı',
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
                'label' => 'Filtreleri sıfırla',
            ],

        ],

        'indicator' => 'Aktif filtreler',

        'multi_select' => [
            'placeholder' => 'Tümü',
        ],

        'select' => [
            'placeholder' => 'Tümü',
        ],

        'trashed' => [

            'label' => 'Silinen kayıtlar',

            'only_trashed' => 'Sadece silinen kayıtlar',

            'with_trashed' => 'Silinen kayıtlar ile',

            'without_trashed' => 'Silinen kayıtlar olmadan',

        ],

    ],

    'reorder_indicator' => 'Sıralamayı değiştirmek için sürükleyin.',

    'selection_indicator' => [

        'selected_count' => '1 kayıt seçildi|:count kayıt seçildi',

        'actions' => [

            'select_all' => [
                'label' => 'Tüm :count kaydı seç',
            ],

            'deselect_all' => [
                'label' => 'Tüm seçimleri kaldır',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sırala',
            ],

            'direction' => [

                'label' => 'Sıralama türü',

                'options' => [
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ],

            ],

        ],

    ],

];

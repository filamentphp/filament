<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'és további :count',
        ],

    ],

    'fields' => [

        'search' => [
            'label' => 'Keresés',
            'placeholder' => 'Keres',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Sorba rendezés befejezése',
        ],

        'enable_reordering' => [
            'label' => 'Sorba rendezés',
        ],

        'filter' => [
            'label' => 'Szűrés',
        ],

        'open_bulk_actions' => [
            'label' => 'Műveletek magjelenítése',
        ],

        'toggle_columns' => [
            'label' => 'Oszlopok mutatása/elrejtése',
        ],

    ],

    'empty' => [
        'heading' => 'Nincs találat',
    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Szűrés megszűntetése',
            ],

            'remove_all' => [
                'label' => 'Összes szűrés megszűntetése',
                'tooltip' => 'Összes szűrés megszűntetése',
            ],

            'reset' => [
                'label' => 'Alapértelmezés',
            ],

        ],

        'indicator' => 'Aktív szűrők',

        'multi_select' => [
            'placeholder' => 'Mind',
        ],

        'select' => [
            'placeholder' => 'Mind',
        ],

        'trashed' => [

            'label' => 'Törölt elemek',

            'only_trashed' => 'Csak a törölt elemek',

            'with_trashed' => 'A törölt elemekkel',

            'without_trashed' => 'A törölt elemek nélkül',

        ],

    ],

    'reorder_indicator' => 'Fogd meg és mozgasd a sorrendezéshez.',

    'selection_indicator' => [

        'selected_count' => '1 elem kiválasztva|:count elem kiválasztva',

        'actions' => [

            'select_all' => [
                'label' => 'Kijelöli mind a(z) :count elemet',
            ],

            'deselect_all' => [
                'label' => 'Kijelölés megszüntetése',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Rendezve',
            ],

            'direction' => [

                'label' => 'Rendezési irány',

                'options' => [
                    'asc' => 'Növekvő',
                    'desc' => 'Csökkenő',
                ],

            ],

        ],

    ],

];

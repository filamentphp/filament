<?php

return [

    'column_toggle' => [

        'heading' => 'Oszlopok',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'és további :count',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Az összes elem kiválasztása vagy megszüntetése tömeges műveletekhez.',
        ],

        'bulk_select_record' => [
            'label' => ':key elem kiválasztása vagy megszüntetése tömeges műveletekhez.',
        ],

        'search' => [
            'label' => 'Keresés',
            'placeholder' => 'Keresés',
            'indicator' => 'Keres',
        ],

    ],

    'summary' => [

        'heading' => 'Összesítés',

        'subheadings' => [
            'all' => 'Összes :label',
            'group' => ':group összesítése',
            'page' => 'Ezen az oldalon',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Átlag',
            ],

            'count' => [
                'label' => 'Darab',
            ],

            'sum' => [
                'label' => 'Összeg',
            ],

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

        'group' => [
            'label' => 'Csoport',
        ],

        'open_bulk_actions' => [
            'label' => 'Műveletek',
        ],

        'toggle_columns' => [
            'label' => 'Oszlopok láthatósága',
        ],

    ],

    'empty' => [

        'heading' => 'Nincs találat',

        'description' => 'Hozzon létre egy újat a kezdéshez.',

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
                'label' => 'Alaphelyzet',
            ],

        ],

        'heading' => 'Szűrők',

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

            'with_trashed' => 'Törölt elemekkel',

            'without_trashed' => 'Törölt elemek nélkül',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Csoportosítás',
                'placeholder' => 'Csoportosítás',
            ],

            'direction' => [

                'label' => 'Csoportosítás iránya',

                'options' => [
                    'asc' => 'Növekvő',
                    'desc' => 'Csökkenő',
                ],

            ],

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

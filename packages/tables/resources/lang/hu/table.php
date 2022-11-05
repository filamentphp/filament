<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'és további :count',
        ],

        'messages' => [
            'copied' => 'Kimásolva',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Keresés',
            'placeholder' => 'Keres',
        ],

    ],

    'pagination' => [

        'label' => 'Lapozás',

        'overview' => ':first től :last ig mutatása a :total találatból',

        'fields' => [

            'records_per_page' => [

                'label' => 'oldalanként',

                'options' => [
                    'all' => 'Összes',
                ],
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Ugrás az oldalra: :page',
            ],

            'next' => [
                'label' => 'Következő',
            ],

            'previous' => [
                'label' => 'Előző',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Sorba rendezés befejezése',
        ],

        'enable_reordering' => [
            'label' => 'Sorba rendezés',
        ],

        'filter' => [
            'label' => 'Szűrés',
        ],

        'open_actions' => [
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

        'buttons' => [

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

        'selected_count' => '1 elem kiválasztva .|:count elem kiválasztva.',

        'buttons' => [

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

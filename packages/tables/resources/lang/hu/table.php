<?php

return [

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

            'reset' => [
                'label' => 'Alapértelmezés',
            ],

            'close' => [
                'label' => 'Bezárás',
            ],

        ],

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

];

<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'ו :count נוספים',
        ],

    ],

    'fields' => [

        'search' => [
            'label' => 'חיפוש',
            'placeholder' => 'חיפוש',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'סיים סידור רשומות',
        ],

        'enable_reordering' => [
            'label' => 'סדר מחדש רשומות',
        ],

        'filter' => [
            'label' => 'פילטר',
        ],

        'open_bulk_actions' => [
            'label' => 'פתח פעולות',
        ],

        'toggle_columns' => [
            'label' => 'הצג עמודות',
        ],

    ],

    'empty' => [
        'heading' => 'לא נמצאו רשומות',
    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'מחק סנן',
            ],

            'remove_all' => [
                'label' => 'מחק את כל המסננים',
                'tooltip' => 'מחק את כל המסננים',
            ],

            'reset' => [
                'label' => 'אפס מסננים',
            ],

        ],

        'indicator' => 'מסננים מאופשרים',

        'multi_select' => [
            'placeholder' => 'הכל',
        ],

        'select' => [
            'placeholder' => 'הכל',
        ],

        'trashed' => [

            'label' => 'רשומות שנמחקו',

            'only_trashed' => 'רק רשומות שנמחקו',

            'with_trashed' => 'גם רשומות שנמחקו',

            'without_trashed' => 'ללא רשומות שנמחקו',

        ],

    ],

    'reorder_indicator' => 'גרור ושחרר רשומות כדי לסדר.',

    'selection_indicator' => [

        'selected_count' => 'רשומה אחת נבחרה|:count רשומות נבחרו',

        'actions' => [

            'select_all' => [
                'label' => 'בחר את כל ה :count',
            ],

            'deselect_all' => [
                'label' => 'ביטול בחירה',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'מיין לפי',
            ],

            'direction' => [

                'label' => 'מיין לפי',

                'options' => [
                    'asc' => 'סדר עולה',
                    'desc' => 'סדר יורד',
                ],

            ],

        ],

    ],

];

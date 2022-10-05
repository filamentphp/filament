<?php

return [

    'columns' => [

        'color' => [

            'messages' => [
                'copied' => 'הועתק',
            ],

        ],

        'tags' => [
            'more' => 'ו :count נוספים',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'חיפוש',
            'placeholder' => 'חיפוש',
        ],

    ],

    'pagination' => [

        'label' => 'הצגת רשומות',

        'overview' => 'מציג :first - :last מתוך :total תוצאות',

        'fields' => [

            'records_per_page' => [

                'label' => 'בעמוד',

                'options' => [
                    'all' => 'הכל',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'נווט לעמוד :page',
            ],

            'next' => [
                'label' => 'הבא',
            ],

            'previous' => [
                'label' => 'הקודם',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'סיים סידור רשומות',
        ],

        'enable_reordering' => [
            'label' => 'סדר מחדש רשומות',
        ],

        'filter' => [
            'label' => 'פילטר',
        ],

        'open_actions' => [
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

        'buttons' => [

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

        'selected_count' => 'רשומה אחת נבחרה.|:count רשומות נבחרו.',

        'buttons' => [

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

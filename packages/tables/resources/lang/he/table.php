<?php

return [

    'column_manager' => [

        'heading' => 'עמודות',

        'actions' => [

            'apply' => [
                'label' => 'החל עמודות',
            ],

            'reset' => [
                'label' => 'איפוס',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'פעולה|פעולות',
        ],

        'select' => [

            'loading_message' => 'טוען...',

            'no_search_results_message' => 'לא נמצאו תוצאות.',

            'placeholder' => 'בחר',

            'searching_message' => 'מחפש...',

            'search_prompt' => 'הקלד כדי לחפש...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'הצג :count פחות',
                'expand_list' => 'הצג עוד :count',
            ],

            'more_list_items' => 'ו-:count פריטים נוספים',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'בחר/בטל בחירה של כל הפריטים לפעולות מרובות.',
        ],

        'bulk_select_record' => [
            'label' => 'בחר/בטל בחירה של פריט :key לפעולות מרובות.',
        ],

        'bulk_select_group' => [
            'label' => 'בחר/בטל בחירה של קבוצה :title לפעולות מרובות.',
        ],

        'search' => [
            'label' => 'חפש',
            'placeholder' => 'חפש',
            'indicator' => 'חיפוש',
        ],

    ],

    'summary' => [

        'heading' => 'סיכום',

        'subheadings' => [
            'all' => 'כל ה:label',
            'group' => 'סיכום של :group',
            'page' => 'בעמוד זה',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'ממוצע',
            ],

            'count' => [
                'label' => 'ספירה',
            ],

            'sum' => [
                'label' => 'סכום',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'סיים סידור רשומות',
        ],

        'enable_reordering' => [
            'label' => 'סדר רשומות מחדש',
        ],

        'filter' => [
            'label' => 'מסננים',
        ],

        'group' => [
            'label' => 'קבוצה',
        ],

        'open_bulk_actions' => [
            'label' => 'פתח פעולות מרובות',
        ],

        'column_manager' => [
            'label' => 'הצג עמודות',
        ],

    ],

    'empty' => [

        'heading' => 'לא נמצאו רשומות',

        'description' => 'צור :model כדי להתחיל.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'החל מסננים',
            ],

            'remove' => [
                'label' => 'הסר מסנן',
            ],

            'remove_all' => [
                'label' => 'הסר את כל המסננים',
                'tooltip' => 'הסר את כל המסננים',
            ],

            'reset' => [
                'label' => 'איפוס מסננים',
            ],

        ],

        'heading' => 'מסננים',

        'indicator' => 'מסננים מופעלים',

        'multi_select' => [
            'placeholder' => 'הכל',
        ],

        'select' => [

            'placeholder' => 'הכל',

            'relationship' => [
                'empty_option_label' => 'ללא',
            ],

        ],

        'trashed' => [

            'label' => 'רשומות שנמחקו',

            'only_trashed' => 'רק רשומות שנמחקו',

            'with_trashed' => 'כולל רשומות שנמחקו',

            'without_trashed' => 'ללא רשומות שנמחקו',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'קבץ לפי',
            ],

            'direction' => [

                'label' => 'כיוון קיבוץ',

                'options' => [
                    'asc' => 'עולה',
                    'desc' => 'יורד',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'גרור ושחרר רשומות כדי לסדר מחדש.',

    'selection_indicator' => [

        'selected_count' => 'נבחרה רשומה אחת|נבחרו :count רשומות',

        'actions' => [

            'select_all' => [
                'label' => 'בחר את כל ה-:count',
            ],

            'deselect_all' => [
                'label' => 'בטל בחירה',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'מיין לפי',
            ],

            'direction' => [

                'label' => 'כיוון מיון',

                'options' => [
                    'asc' => 'סדר עולה',
                    'desc' => 'סדר יורד',
                ],

            ],

        ],

    ],

    'default_model_label' => 'רשומה',

];

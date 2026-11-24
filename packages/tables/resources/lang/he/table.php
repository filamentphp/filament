<?php

return [

    'column_manager' => [

        'heading' => 'עמודות',

        'position' => 'מיקום :position מתוך :count.',

        'position_in_group' => 'מיקום :position מתוך :count בקבוצה :group.',

        'reordered' => 'העמודה :column הועברה למיקום :position מתוך :count.',

        'reordered_deferred' => 'העמודה :column הועברה למיקום :position מתוך :count. החל את העמודות כדי לשמור את השינויים.',

        'reordered_in_group' => 'העמודה :column הועברה למיקום :position מתוך :count בקבוצה :group.',

        'reordered_in_group_deferred' => 'העמודה :column הועברה למיקום :position מתוך :count בקבוצה :group. החל את העמודות כדי לשמור את השינויים.',

        'actions' => [

            'apply' => [
                'label' => 'החל עמודות',
            ],

            'move_up' => [
                'label' => 'הזז את :column למעלה',
            ],

            'move_down' => [
                'label' => 'הזז את :column למטה',
            ],

            'reorder' => [
                'label' => 'שינוי סדר העמודה',
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

        'editable' => [
            'saving' => 'שומר שינוי...',
            'saved' => 'השינוי נשמר.',
            'failure' => 'לא ניתן היה לאשר את השמירה. רענן את הדף לפני שתנסה שוב.',
            'unavailable' => 'לא ניתן עוד לערוך פריט זה.',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'כן',
                'false' => 'לא',
            ],

        ],

        'select' => [

            'loading_message' => 'טוען...',

            'no_options_message' => 'אין אפשרויות זמינות.',

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

        'reorder_record' => [
            'label' => 'שינוי סדר הפריט :key',
        ],

        'move_record_up' => [
            'label' => 'הזז את :label למעלה',
        ],

        'move_record_down' => [
            'label' => 'הזז את :label למטה',
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

        'toggle_record_content' => [
            'label' => 'הרחבה/צמצום של הפריט :key',
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

    'loading' => 'טוען...',

    'reorder_indicator' => 'גרור ושחרר רשומות כדי לסדר מחדש.',

    'reordering' => [
        'position' => ':label, מיקום :position מתוך :count.',
        'moved' => ':label הועבר למיקום :position מתוך :count.',
    ],

    'result_count' => '{0} אין תוצאות|{1} תוצאה אחת|[2,*] :count תוצאות',

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

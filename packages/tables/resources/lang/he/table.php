<?php

return [

    'column_toggle' => [
        'heading' => 'עמודות',
    ],

    'columns' => [
        'text' => [
            'more_list_items' => 'ו-:count פריטים נוספים',
        ],
    ],

    'fields' => [
        'bulk_select_page' => [
            'label' => 'בחר/בטל בחירה לפעולות המרובות.',
        ],
        'bulk_select_record' => [
            'label' => 'בחר/בטל בחירה לפעולות המרובות לפריט :key.',
        ],
        'search' => [
            'label' => 'חיפוש',
            'placeholder' => 'חיפוש',
            'indicator' => 'חיפוש',
        ],
    ],

    'summary' => [
        'heading' => 'סיכום',
        'subheadings' => [
            'all' => 'כל ה-:label',
            'group' => 'סיכום של :group',
            'page' => 'עמוד זה',
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
            'label' => 'סדר מחדש רשומות',
        ],
        'filter' => [
            'label' => 'פילטר',
        ],
        'group' => [
            'label' => 'קבוצה',
        ],
        'open_bulk_actions' => [
            'label' => 'פתח פעולות מרובות',
        ],
        'toggle_columns' => [
            'label' => 'הצג עמודות',
        ],
    ],

    'empty' => [
        'heading' => 'לא נמצאו רשומות',
        'description' => 'צור :model כדי להתחיל.',
    ],

    'filters' => [
        'actions' => [
            'remove' => [
                'label' => 'הסר סנן',
            ],
            'remove_all' => [
                'label' => 'הסר את כל הסננים',
                'tooltip' => 'הסר את כל הסננים',
            ],
            'reset' => [
                'label' => 'איפוס סננים',
            ],
        ],
        'heading' => 'סננים',
        'indicator' => 'סננים מופעלים',
        'multi_select' => [
            'placeholder' => 'הכל',
        ],
        'select' => [
            'placeholder' => 'הכל',
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
                'placeholder' => 'קבץ לפי',
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

    'reorder_indicator' => 'גרור ושחרר רשומות לסידור מחדש.',

    'selection_indicator' => [
        'selected_count' => 'נבחרה רשומה אחת|נבחרו :count רשומות',
        'actions' => [
            'select_all' => [
                'label' => 'בחר את כל :count',
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
                'label' => 'סדר לפי',
                'options' => [
                    'asc' => 'סדר עולה',
                    'desc' => 'סדר יורד',
                ],
            ],
        ],
    ],

];

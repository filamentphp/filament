<?php

return [

    'column_manager' => [

        'heading' => 'کالمز',

        'actions' => [

            'apply' => [
                'label' => 'کالمز لاگو کریں',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'عمل|اعمال',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => ':count کم دکھائیں',
                'expand_list' => ':count مزید دکھائیں',
            ],

            'more_list_items' => 'اور :count مزید',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'اس صفحے کے تمام آئٹمز کو اکٹھا منتخب/منسوخ کریں۔',
        ],

        'bulk_select_record' => [
            'label' => 'آئٹم :key کو اکٹھا عمل کرنے کے لیے منتخب/منسوخ کریں۔',
        ],

        'bulk_select_group' => [
            'label' => 'گروپ :title کو اکٹھا عمل کرنے کے لیے منتخب/منسوخ کریں۔',
        ],

        'search' => [
            'label' => 'تلاش کریں',
            'placeholder' => 'تلاش کریں',
            'indicator' => 'تلاش',
        ],

    ],

    'summary' => [

        'heading' => 'خلاصہ',

        'subheadings' => [
            'all' => 'تمام :label',
            'group' => ':group کا خلاصہ',
            'page' => 'یہ صفحہ',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'اوسط',
            ],

            'count' => [
                'label' => 'گنتی',
            ],

            'sum' => [
                'label' => 'مجموعہ',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'ریکارڈز کی ترتیب مکمل کریں',
        ],

        'enable_reordering' => [
            'label' => 'ریکارڈز کو دوبارہ ترتیب دیں',
        ],

        'filter' => [
            'label' => 'فلٹر کریں',
        ],

        'group' => [
            'label' => 'گروپ کریں',
        ],

        'open_bulk_actions' => [
            'label' => 'اکٹھے اعمال',
        ],

        'column_manager' => [
            'label' => 'کالم مینیجر',
        ],

    ],

    'empty' => [

        'heading' => 'کوئی :model نہیں',

        'description' => ':model بنانے کے لیے شروع کریں۔',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'فلٹر لاگو کریں',
            ],

            'remove' => [
                'label' => 'فلٹر ہٹائیں',
            ],

            'remove_all' => [
                'label' => 'تمام فلٹر ہٹائیں',
                'tooltip' => 'تمام فلٹر ہٹائیں',
            ],

            'reset' => [
                'label' => 'ری سیٹ کریں',
            ],

        ],

        'heading' => 'فلٹرز',

        'indicator' => 'فعال فلٹرز',

        'multi_select' => [
            'placeholder' => 'تمام',
        ],

        'select' => [

            'placeholder' => 'تمام',

            'relationship' => [
                'empty_option_label' => 'کوئی نہیں',
            ],

        ],

        'trashed' => [

            'label' => 'حذف شدہ ریکارڈز',

            'only_trashed' => 'صرف حذف شدہ ریکارڈز',

            'with_trashed' => 'حذف شدہ ریکارڈز کے ساتھ',

            'without_trashed' => 'بغیر حذف شدہ ریکارڈز',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'اس پر گروپ کریں',
                'placeholder' => 'گروپ کریں',
            ],

            'direction' => [

                'label' => 'گروپ کی سمت',

                'options' => [
                    'asc' => 'صعودی (Ascending)',
                    'desc' => 'نزولی (Descending)',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'ریکارڈز کو ترتیب دینے کے لیے ڈریگ اینڈ ڈراپ کریں۔',

    'selection_indicator' => [

        'selected_count' => '1 ریکارڈ منتخب کیا گیا|:count ریکارڈز منتخب کیے گئے',

        'actions' => [

            'select_all' => [
                'label' => 'تمام :count منتخب کریں',
            ],

            'deselect_all' => [
                'label' => 'سب کو غیر منتخب کریں',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'اس پر ترتیب دیں',
            ],

            'direction' => [

                'label' => 'ترتیب کی سمت',

                'options' => [
                    'asc' => 'صعودی (Ascending)',
                    'desc' => 'نزولی (Descending)',
                ],

            ],

        ],

    ],

    'default_model_label' => 'ریکارڈ',

];

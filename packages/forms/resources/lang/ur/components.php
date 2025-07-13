<?php

return [

    'builder' => [
        'actions' => [
            'clone' => ['label' => 'نقل بنائیں'],
            'add' => [
                'label' => ':label میں شامل کریں',
                'modal' => [
                    'heading' => ':label میں شامل کریں',
                    'actions' => [
                        'add' => ['label' => 'شامل کریں'],
                    ],
                ],
            ],
            'add_between' => [
                'label' => 'درمیان میں داخل کریں',
                'modal' => [
                    'heading' => ':label میں شامل کریں',
                    'actions' => [
                        'add' => ['label' => 'شامل کریں'],
                    ],
                ],
            ],
            'delete' => ['label' => 'حذف کریں'],
            'edit' => [
                'label' => 'ترمیم کریں',
                'modal' => [
                    'heading' => 'بلاک ترمیم کریں',
                    'actions' => [
                        'save' => ['label' => 'تبدیلیاں محفوظ کریں'],
                    ],
                ],
            ],
            'reorder' => ['label' => 'منتقل کریں'],
            'move_down' => ['label' => 'نیچے کریں'],
            'move_up' => ['label' => 'اوپر کریں'],
            'collapse' => ['label' => 'سمیٹیں'],
            'expand' => ['label' => 'پھیلائیں'],
            'collapse_all' => ['label' => 'سب سمیٹیں'],
            'expand_all' => ['label' => 'سب پھیلائیں'],
        ],
    ],

    'checkbox_list' => [
        'actions' => [
            'deselect_all' => ['label' => 'سب غیر منتخب کریں'],
            'select_all' => ['label' => 'سب منتخب کریں'],
        ],
    ],

    'file_upload' => [
        'editor' => [
            'actions' => [
                'cancel' => ['label' => 'منسوخ کریں'],
                'drag_crop' => ['label' => 'کٹائی موڈ منتخب کریں'],
                'drag_move' => ['label' => 'منتقل موڈ منتخب کریں'],
                'flip_horizontal' => ['label' => 'تصویر افقی پلٹائیں'],
                'flip_vertical' => ['label' => 'تصویر عمودی پلٹائیں'],
                'move_down' => ['label' => 'تصویر نیچے منتقل کریں'],
                'move_left' => ['label' => 'تصویر بائیں منتقل کریں'],
                'move_right' => ['label' => 'تصویر دائیں منتقل کریں'],
                'move_up' => ['label' => 'تصویر اوپر منتقل کریں'],
                'reset' => ['label' => 'ری سیٹ کریں'],
                'rotate_left' => ['label' => 'بائیں گھمائیں'],
                'rotate_right' => ['label' => 'دائیں گھمائیں'],
                'set_aspect_ratio' => ['label' => ':ratio تناسب مقرر کریں'],
                'save' => ['label' => 'محفوظ کریں'],
                'zoom_100' => ['label' => '100% زوم'],
                'zoom_in' => ['label' => 'زوم ان کریں'],
                'zoom_out' => ['label' => 'زوم آؤٹ کریں'],
            ],
            'fields' => [
                'height' => ['label' => 'اونچائی', 'unit' => 'px'],
                'rotation' => ['label' => 'گھوماؤ', 'unit' => 'ڈگری'],
                'width' => ['label' => 'چوڑائی', 'unit' => 'px'],
                'x_position' => ['label' => 'محور X', 'unit' => 'px'],
                'y_position' => ['label' => 'محور Y', 'unit' => 'px'],
            ],
            'aspect_ratios' => [
                'label' => 'تناسب',
                'no_fixed' => ['label' => 'آزادی سے'],
            ],
            'svg' => [
                'messages' => [
                    'confirmation' => 'SVG فائل ایڈیٹنگ کے دوران معیار خراب ہو سکتا ہے۔ کیا آپ جاری رکھنا چاہتے ہیں؟',
                    'disabled' => 'SVG ایڈیٹنگ غیر فعال ہے کیونکہ معیار متاثر ہو سکتا ہے۔',
                ],
            ],
        ],
    ],

    'key_value' => [
        'actions' => [
            'add' => ['label' => 'نئی قطار شامل کریں'],
            'delete' => ['label' => 'قطار حذف کریں'],
            'reorder' => ['label' => 'قطار ترتیب دیں'],
        ],
        'fields' => [
            'key' => ['label' => 'کلید'],
            'value' => ['label' => 'قدر'],
        ],
    ],

    'markdown_editor' => [
        'tools' => [
            'attach_files' => 'فائلز منسلک کریں',
            'blockquote' => 'اقتباس',
            'bold' => 'بولڈ',
            'bullet_list' => 'نقطہ وار فہرست',
            'code_block' => 'کوڈ بلاک',
            'heading' => 'سرخی',
            'italic' => 'ایتالک',
            'link' => 'لنک',
            'ordered_list' => 'نمر دار فہرست',
            'redo' => 'دوبارہ کریں',
            'strike' => 'سلٹر تھرو',
            'table' => 'ٹیبل',
            'undo' => 'واپس کریں',
        ],
    ],

    'modal_table_select' => [
        'actions' => [
            'select' => [
                'label' => 'منتخب کریں',
                'actions' => ['select' => ['label' => 'منتخب کریں']],
            ],
        ],
    ],

    'radio' => [
        'boolean' => [
            'true' => 'ہاں',
            'false' => 'نہیں',
        ],
    ],

    'repeater' => [
        'actions' => [
            'add' => ['label' => ':label میں شامل کریں'],
            'add_between' => ['label' => 'درمیان شامل کریں'],
            'delete' => ['label' => 'حذف کریں'],
            'clone' => ['label' => 'نقل بنائیں'],
            'reorder' => ['label' => 'منتقل کریں'],
            'move_down' => ['label' => 'نیچے جائیں'],
            'move_up' => ['label' => 'اوپر جائیں'],
            'collapse' => ['label' => 'سمیٹیں'],
            'expand' => ['label' => 'پھیلائیں'],
            'collapse_all' => ['label' => 'سب سمیٹیں'],
            'expand_all' => ['label' => 'سب پھیلائیں'],
        ],
    ],

    'rich_editor' => [
        'actions' => [
            'attach_files' => [
                'label' => 'فائل اپلوڈ کریں',
                'modal' => [
                    'heading' => 'فائل اپلوڈ کریں',
                    'form' => [
                        'file' => ['label' => ['new' => 'فائل', 'existing' => 'فائل بدلیں']],
                        'alt' => ['label' => ['new' => 'Alt متن', 'existing' => 'Alt متن بدلیں']],
                    ],
                ],
            ],
            'custom_block' => ['modal' => ['actions' => ['insert' => ['label' => 'داخل کریں'], 'save' => ['label' => 'محفوظ کریں']]]],
            'link' => [
                'label' => 'ترمیم کریں',
                'modal' => [
                    'heading' => 'لنک',
                    'form' => [
                        'url' => ['label' => 'URL'],
                        'should_open_in_new_tab' => ['label' => 'نئی ٹیب میں کھولیں'],
                    ],
                ],
            ],
        ],
        'no_merge_tag_search_results_message' => 'کوئی مرج ٹیگ نہیں ملا۔',
        'tools' => [
            'align_center' => 'مرکز میں سیدھ کریں',
            'align_end' => 'دائیں سیدھ کریں',
            'align_justify' => 'پورے متن میں سیدھ کریں',
            'align_start' => 'بائیں سیدھ کریں',
            'attach_files' => 'فائلز منسلک کریں',
            'blockquote' => 'اقتباس',
            'bold' => 'بولڈ',
            'bullet_list' => 'نقطوں میں فہرست',
            'clear_formatting' => 'فارمیٹنگ صاف کریں',
            'code_block' => 'کوڈ بلاک',
            'custom_blocks' => 'بلاکس',
            'details' => 'تفصیلات',
            'h1' => 'عنوان',
            'h2' => 'سرخی',
            'h3' => 'سب سرخی',
            'highlight' => 'نمایاں کریں',
            'horizontal_rule' => 'افقی لائن',
            'italic' => 'ایتالک',
            'lead' => 'لیڈ متن',
            'link' => 'لنک',
            'merge_tags' => 'مرج ٹیگز',
            'ordered_list' => 'نمر دار فہرست',
            'redo' => 'دوبارہ کریں',
            'small' => 'چھوٹا متن',
            'strike' => 'سلٹر تھرو',
            'subscript' => 'سبسکرپٹ',
            'superscript' => 'سپر سکرپٹ',
            'table' => 'ٹیبل',
            'table_delete' => 'ٹیبل حذف کریں',
            'table_add_column_before' => 'کالم شامل کریں (قبل)',
            'table_add_column_after' => 'کالم شامل کریں (بعد)',
            'table_delete_column' => 'کالم حذف کریں',
            'table_add_row_before' => 'قطار شامل کریں (اوپر)',
            'table_add_row_after' => 'قطار شامل کریں (نیچے)',
            'table_delete_row' => 'قطار حذف کریں',
            'table_merge_cells' => 'سیلز ملا دیں',
            'table_split_cell' => 'سیل تقسیم کریں',
            'table_toggle_header_row' => 'ہیڈر قطار فعال/غیر فعال کریں',
            'underline' => 'انڈر لائن',
            'undo' => 'واپس کریں',
        ],
    ],

    'select' => [
        'actions' => [
            'create_option' => [
                'label' => 'نیا بنائیں',
                'modal' => [
                    'heading' => 'نیا بنائیں',
                    'actions' => ['create' => ['label' => 'بنائیں'], 'create_another' => ['label' => 'بنائیں اور مزید بنائیں']],
                ],
            ],
            'edit_option' => [
                'label' => 'ترمیم کریں',
                'modal' => [
                    'heading' => 'ترمیم کریں',
                    'actions' => ['save' => ['label' => 'محفوظ کریں']],
                ],
            ],
        ],
        'boolean' => ['true' => 'ہاں', 'false' => 'نہیں'],
        'loading_message' => 'لوڈ ہو رہا ہے...',
        'max_items_message' => 'صرف :count منتخب ہو سکتے ہیں۔',
        'no_search_results_message' => 'کوئی آپشن نہیں ملا۔',
        'placeholder' => 'ایک آپشن منتخب کریں',
        'searching_message' => 'تلاش ہو رہا ہے...',
        'search_prompt' => 'تلاش شروع کرنے کے لیے لکھیں...',
    ],

    'tags_input' => ['placeholder' => 'نیا ٹیگ'],

    'text_input' => [
        'actions' => [
            'hide_password' => ['label' => 'پاسورڈ چھپائیں'],
            'show_password' => ['label' => 'پاسورڈ دکھائیں'],
        ],
    ],

    'toggle_buttons' => ['boolean' => ['true' => 'ہاں', 'false' => 'نہیں']],

];

<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'همسان‌سازی',
            ],

            'add' => [

                'label' => 'افزودن به :label',

                'modal' => [

                    'heading' => 'افزودن به :label',

                    'actions' => [

                        'add' => [
                            'label' => 'افزودن',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'درج',

                'modal' => [

                    'heading' => 'افزودن به :label',

                    'actions' => [

                        'add' => [
                            'label' => 'افزودن',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'حذف',
            ],

            'edit' => [

                'label' => 'ویرایش',

                'modal' => [

                    'heading' => 'ویرایش',

                    'actions' => [

                        'save' => [
                            'label' => 'ذخیره تغییرات',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'جابه‌جایی',
            ],

            'move_down' => [
                'label' => 'پایین آوردن',
            ],

            'move_up' => [
                'label' => 'بالا بردن',
            ],

            'collapse' => [
                'label' => 'جمع کردن',
            ],

            'expand' => [
                'label' => 'باز کردن',
            ],

            'collapse_all' => [
                'label' => 'جمع کردن همه',
            ],

            'expand_all' => [
                'label' => 'باز کردن همه',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'لغو انتخاب همه',
            ],

            'select_all' => [
                'label' => 'انتخاب همه',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'لغو',
                ],

                'drag_crop' => [
                    'label' => 'حالت کشیدن «برش»',
                ],

                'drag_move' => [
                    'label' => 'حالت کشیدن «حرکت»',
                ],

                'flip_horizontal' => [
                    'label' => 'برگردان افقی عکس',
                ],

                'flip_vertical' => [
                    'label' => 'برگردان عمودی عکس',
                ],

                'move_down' => [
                    'label' => 'به پایین بردن عکس',
                ],

                'move_left' => [
                    'label' => 'به چپ بردن عکس',
                ],

                'move_right' => [
                    'label' => 'به راست بردن عکس',
                ],

                'move_up' => [
                    'label' => 'به بالا بردن عکس',
                ],

                'reset' => [
                    'label' => 'بازنشانی',
                ],

                'rotate_left' => [
                    'label' => 'چرخاندن عکس به چپ',
                ],

                'rotate_right' => [
                    'label' => 'چرخاندن عکس به راست',
                ],

                'set_aspect_ratio' => [
                    'label' => 'تنظیم نسبت ابعاد به :ratio',
                ],

                'save' => [
                    'label' => 'ذخیره',
                ],

                'zoom_100' => [
                    'label' => 'بزرگنمایی عکس به ۱۰۰٪',
                ],

                'zoom_in' => [
                    'label' => 'بزرگنمایی',
                ],

                'zoom_out' => [
                    'label' => 'کوچک‌نمایی',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'ارتفاع',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'چرخش',
                    'unit' => 'درجه',
                ],

                'width' => [
                    'label' => 'عرض',
                    'unit' => 'px',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'px',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'px',
                ],

            ],

            'aspect_ratios' => [

                'label' => 'نسبت ابعاد',

                'no_fixed' => [
                    'label' => 'آزاد',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'ویرایش فایل‌های SVG توصیه نمی‌شود، زیرا ممکن است کیفیت هنگام مقیاس‌بندی کاهش یابد.\n آیا مطمئن هستید که می‌خواهید ادامه دهید؟',
                    'disabled' => 'ویرایش فایل‌های SVG غیرفعال است زیرا ممکن است کیفیت هنگام مقیاس‌بندی کاهش یابد.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'افزودن ردیف',
            ],

            'delete' => [
                'label' => 'حذف ردیف',
            ],

            'reorder' => [
                'label' => 'بازچینش ردیف',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'کلید',
            ],

            'value' => [
                'label' => 'مقدار',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'فایل‌های بارگذاری شده باید از نوع: :values باشند.',

        'file_attachments_max_size_message' => 'حجم فایل‌های بارگذاری شده نباید بیشتر از :max کیلوبایت باشد.',

        'tools' => [
            'attach_files' => 'پیوستن فایل‌ها',
            'blockquote' => 'نقل قول',
            'bold' => 'پررنگ',
            'bullet_list' => 'لیست نامرتب',
            'code_block' => 'بلوک کد',
            'heading' => 'عنوان',
            'italic' => 'مورب',
            'link' => 'لینک',
            'ordered_list' => 'لیست مرتب',
            'redo' => 'جلو',
            'strike' => 'خط زده',
            'table' => 'جدول',
            'undo' => 'عقب',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'انتخاب',

                'actions' => [

                    'select' => [
                        'label' => 'انتخاب کردن',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'بله',
            'false' => 'خیر',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'افزودن به :label',
            ],

            'add_between' => [
                'label' => 'درج بین',
            ],

            'delete' => [
                'label' => 'حذف',
            ],

            'clone' => [
                'label' => 'همسان‌سازی',
            ],

            'reorder' => [
                'label' => 'جابه‌جایی',
            ],

            'move_down' => [
                'label' => 'پایین آوردن',
            ],

            'move_up' => [
                'label' => 'بالا بردن',
            ],

            'collapse' => [
                'label' => 'جمع کردن',
            ],

            'expand' => [
                'label' => 'باز کردن',
            ],

            'collapse_all' => [
                'label' => 'جمع کردن همه',
            ],

            'expand_all' => [
                'label' => 'باز کردن همه',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'بارگذاری فایل',

                'modal' => [

                    'heading' => 'بارگذاری فایل',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'فایل',
                                'existing' => 'جایگزینی فایل',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'متن جایگزین',
                                'existing' => 'تغییر متن جایگزین',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'درج',
                        ],

                        'save' => [
                            'label' => 'ذخیره',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'شبکه',

                'modal' => [

                    'heading' => 'شبکه',

                    'form' => [

                        'preset' => [

                            'label' => 'پیش‌تنظیم',

                            'placeholder' => 'هیچ',

                            'options' => [
                                'two' => 'دو',
                                'three' => 'سه',
                                'four' => 'چهار',
                                'five' => 'پنج',
                                'two_start_third' => 'دو (شروع سوم)',
                                'two_end_third' => 'دو (پایان سوم)',
                                'two_start_fourth' => 'دو (شروع چهارم)',
                                'two_end_fourth' => 'دو (پایان چهارم)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'ستون‌ها',
                        ],

                        'from_breakpoint' => [

                            'label' => 'از نقطه شکست',

                            'options' => [
                                'default' => 'همه',
                                'sm' => 'کوچک',
                                'md' => 'متوسط',
                                'lg' => 'بزرگ',
                                'xl' => 'خیلی بزرگ',
                                '2xl' => 'دو برابر خیلی بزرگ',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'دو ستون نامتقارن',
                        ],

                        'start_span' => [
                            'label' => 'شروع بازه',
                        ],

                        'end_span' => [
                            'label' => 'پایان بازه',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'ویرایش',

                'modal' => [

                    'heading' => 'لینک',

                    'form' => [

                        'url' => [
                            'label' => 'آدرس اینترنتی',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'بازکردن در برگه جدید',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'رنگ متن',

                'modal' => [

                    'heading' => 'رنگ متن',

                    'form' => [

                        'color' => [
                            'label' => 'رنگ',
                        ],

                        'custom_color' => [
                            'label' => 'رنگ سفارشی',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'فایل‌های بارگذاری شده باید از نوع: :values باشند.',

        'file_attachments_max_size_message' => 'حجم فایل‌های بارگذاری شده نباید بیشتر از :max کیلوبایت باشد.',

        'no_merge_tag_search_results_message' => 'هیچ نتیجه‌ای برای جستجوی برچسب ادغام یافت نشد.',

        'tools' => [
            'align_center' => 'تراز وسط',
            'align_end' => 'تراز انتها',
            'align_justify' => 'تراز کامل',
            'align_start' => 'تراز ابتدا',
            'attach_files' => 'پیوستن فایل‌ها',
            'blockquote' => 'نقل قول',
            'bold' => 'پررنگ',
            'bullet_list' => 'لیست نامرتب',
            'clear_formatting' => 'پاک کردن قالب‌بندی',
            'code' => 'کد',
            'code_block' => 'بلوک کد',
            'custom_blocks' => 'بلوک‌های سفارشی',
            'details' => 'جزئیات',
            'h1' => 'عنوان اصلی',
            'h2' => 'عنوان فرعی',
            'h3' => 'زیرعنوان',
            'grid' => 'شبکه',
            'grid_delete' => 'حذف شبکه',
            'highlight' => 'برجسته‌سازی',
            'horizontal_rule' => 'خط افقی',
            'italic' => 'مورب',
            'lead' => 'متن اصلی',
            'link' => 'لینک',
            'merge_tags' => 'برچسب‌های ادغام',
            'ordered_list' => 'لیست مرتب',
            'redo' => 'جلو',
            'small' => 'متن کوچک',
            'strike' => 'خط زده',
            'subscript' => 'زیرنویس',
            'superscript' => 'بالانویس',
            'table' => 'جدول',
            'table_delete' => 'حذف جدول',
            'table_add_column_before' => 'افزودن ستون قبل',
            'table_add_column_after' => 'افزودن ستون بعد',
            'table_delete_column' => 'حذف ستون',
            'table_add_row_before' => 'افزودن ردیف بالا',
            'table_add_row_after' => 'افزودن ردیف پایین',
            'table_delete_row' => 'حذف ردیف',
            'table_merge_cells' => 'ادغام سلول‌ها',
            'table_split_cell' => 'تقسیم سلول',
            'table_toggle_header_row' => 'تغییر ردیف سرستون',
            'table_toggle_header_cell' => 'تغییر سلول سرستون',
            'text_color' => 'رنگ متن',
            'underline' => 'زیرخط',
            'undo' => 'عقب',
        ],

        'uploading_file_message' => 'در حال بارگذاری فایل...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'ایجاد',

                'modal' => [

                    'heading' => 'ایجاد',

                    'actions' => [

                        'create' => [
                            'label' => 'ایجاد',
                        ],

                        'create_another' => [
                            'label' => 'ایجاد و ایجاد یکی دیگر',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'ویرایش',

                'modal' => [

                    'heading' => 'ویرایش',

                    'actions' => [

                        'save' => [
                            'label' => 'ذخیره',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'بله',
            'false' => 'خیر',
        ],

        'loading_message' => 'در حال بارگذاری...',

        'max_items_message' => 'تنها :count مورد می‌تواند انتخاب شود.',

        'no_search_results_message' => 'هیچ گزینه‌ای با جستجوی شما مطابقت ندارد.',

        'placeholder' => 'یک گزینه را انتخاب کنید',

        'searching_message' => 'در حال جستجو...',

        'search_prompt' => 'برای جستجو تایپ کنید...',

    ],

    'tags_input' => [
        'placeholder' => 'تگ جدید',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'کپی',
                'message' => 'کپی شد',
            ],

            'hide_password' => [
                'label' => 'مخفی کردن رمز',
            ],

            'show_password' => [
                'label' => 'نمایش رمز',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'بله',
            'false' => 'خیر',
        ],

    ],

];

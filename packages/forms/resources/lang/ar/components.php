<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'نسخ',
            ],

            'add' => [

                'label' => 'إضافة إلى :label',

                'modal' => [

                    'heading' => 'إضافة إلى :label',

                    'actions' => [

                        'add' => [
                            'label' => 'إضافة',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'إدراج بين الوحدات',

                'modal' => [

                    'heading' => 'إضافة إلى  :label',

                    'actions' => [

                        'add' => [
                            'label' => 'إضافة',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'حذف',
            ],

            'edit' => [

                'label' => 'تعديل',

                'modal' => [

                    'heading' => 'تعديل القسم',

                    'actions' => [

                        'save' => [
                            'label' => 'حفظ التغييرات',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'نقل',
            ],

            'move_down' => [
                'label' => 'تحريك لأسفل',
            ],

            'move_up' => [
                'label' => 'تحريك لأعلى',
            ],

            'collapse' => [
                'label' => 'طيّ',
            ],

            'expand' => [
                'label' => 'توسيع',
            ],

            'collapse_all' => [
                'label' => 'طيّ الكل',
            ],

            'expand_all' => [
                'label' => 'توسيع الكل',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'إلغاء تحديد الكل',
            ],

            'select_all' => [
                'label' => 'تحديد الكل',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'إلغاء',
                ],

                'drag_crop' => [
                    'label' => 'وضع السحب "قص"',
                ],

                'drag_move' => [
                    'label' => 'وضع السحب "تحريك"',
                ],

                'flip_horizontal' => [
                    'label' => 'قلب الصورة أفقياً',
                ],

                'flip_vertical' => [
                    'label' => 'قلب الصورة عمودياً',
                ],

                'move_down' => [
                    'label' => 'تحريك الصورة لأسفل',
                ],

                'move_left' => [
                    'label' => 'تحريك الصورة لليسار',
                ],

                'move_right' => [
                    'label' => 'تحريك الصورة لليمين',
                ],

                'move_up' => [
                    'label' => 'تحريك الصورة لأعلى',
                ],

                'reset' => [
                    'label' => 'استعادة',
                ],

                'rotate_left' => [
                    'label' => 'تدوير الصورة لليسار',
                ],

                'rotate_right' => [
                    'label' => 'تدوير الصورة لليمين',
                ],

                'set_aspect_ratio' => [
                    'label' => 'تعيين نسبة العرض للإرتفاع إلى :ratio',
                ],

                'save' => [
                    'label' => 'حفظ',
                ],

                'zoom_100' => [
                    'label' => 'تحجيم الصورة إلى 100%',
                ],

                'zoom_in' => [
                    'label' => 'تكبير',
                ],

                'zoom_out' => [
                    'label' => 'تصغير',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'الارتفاع',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'الدوران',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'العرض',
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

                'label' => 'نسبة الأبعاد',

                'no_fixed' => [
                    'label' => 'حر',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'لا يوصى بتحرير ملفات SVG لأنه قد يؤدي إلى فقدان الجودة عند تغيير الحجم.\n هل أنت متأكد من رغبتك في المتابعة؟',
                    'disabled' => 'تم تعطيل تحرير ملفات SVG لأنه قد يؤدي إلى فقدان الجودة عند تغيير الحجم.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'إضافة صف',
            ],

            'delete' => [
                'label' => 'حذف صف',
            ],

            'reorder' => [
                'label' => 'إعادة ترتيب الصف',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'المفتاح',
            ],

            'value' => [
                'label' => 'القيمة',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'يجب أن تكون الملفات المرفوعة من نوع: :values.',

        'file_attachments_max_size_message' => 'يجب ألا يتجاوز حجم الملفات المرفوعة :max كيلوبايت.',

        'tools' => [
            'attach_files' => 'إرفاق ملفات',
            'blockquote' => 'اقتباس',
            'bold' => 'عريض',
            'bullet_list' => 'قائمة نقطية',
            'code_block' => 'نص برمجي',
            'heading' => 'العناوين',
            'italic' => 'مائل',
            'link' => 'رابط تشعبي',
            'ordered_list' => 'قائمة رقمية',
            'redo' => 'إعادة',
            'strike' => 'يتوسطه خط',
            'table' => 'جدول',
            'undo' => 'تراجع',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'تحديد',

                'actions' => [

                    'select' => [
                        'label' => 'تحديد',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'نعم',
            'false' => 'لا',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'إضافة إلى :label',
            ],

            'add_between' => [
                'label' => 'إدراج بين',
            ],

            'delete' => [
                'label' => 'حذف',
            ],

            'clone' => [
                'label' => 'نسخ',
            ],

            'reorder' => [
                'label' => 'نقل',
            ],

            'move_down' => [
                'label' => 'تحريك لأسفل',
            ],

            'move_up' => [
                'label' => 'تحريك لأعلى',
            ],

            'collapse' => [
                'label' => 'طيّ',
            ],

            'expand' => [
                'label' => 'توسيع',
            ],

            'collapse_all' => [
                'label' => 'طيّ الكل',
            ],

            'expand_all' => [
                'label' => 'توسيع الكل',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'رفع ملف',

                'modal' => [

                    'heading' => 'رفع ملف',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'الملف',
                                'existing' => 'استبدال الملف',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'النص البديل',
                                'existing' => 'تعديل النص البديل',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'إدراج',
                        ],

                        'save' => [
                            'label' => 'حفظ',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'شبكة',

                'modal' => [

                    'heading' => 'شبكة',

                    'form' => [

                        'preset' => [

                            'label' => 'نمط جاهز',

                            'placeholder' => 'بدون',

                            'options' => [
                                'two' => 'عمودان',
                                'three' => 'ثلاث أعمدة',
                                'four' => 'أربع أعمدة',
                                'five' => 'خمس أعمدة',
                                'two_start_third' => 'عمودان (الثلث في البداية)',
                                'two_end_third' => 'عمودان (الثلث في النهاية)',
                                'two_start_fourth' => 'عمودان (الربع في البداية)',
                                'two_end_fourth' => 'عمودان (الربع في النهاية)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'الأعمدة',
                        ],

                        'from_breakpoint' => [

                            'label' => 'من نقطة التوقف',

                            'options' => [
                                'default' => 'الكل',
                                'sm' => 'صغير',
                                'md' => 'متوسط',
                                'lg' => 'كبير',
                                'xl' => 'كبير جداً',
                                '2xl' => 'كبير جداً جداً',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'عمودين غير متماثلين',
                        ],

                        'start_span' => [
                            'label' => 'امتداد البداية',
                        ],

                        'end_span' => [
                            'label' => 'امتداد النهاية',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'تعديل',

                'modal' => [

                    'heading' => 'رابط',

                    'form' => [

                        'url' => [
                            'label' => 'الرابط',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'فتح في تبويب جديد',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'لون النص',

                'modal' => [

                    'heading' => 'لون النص',

                    'form' => [

                        'color' => [
                            'label' => 'اللون',
                        ],

                        'custom_color' => [
                            'label' => 'لون مخصص',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'يجب أن تكون الملفات المرفوعة من نوع: :values.',

        'file_attachments_max_size_message' => 'يجب ألا يتجاوز حجم الملفات المرفوعة :max كيلوبايت.',

        'no_merge_tag_search_results_message' => 'لا توجد نتائج لوسوم الدمج.',

        'tools' => [
            'align_center' => 'محاذاة للوسط',
            'align_end' => 'محاذاة للنهاية',
            'align_justify' => 'محاذاة للضبط',
            'align_start' => 'محاذاة للبداية',
            'attach_files' => 'إرفاق ملفات',
            'blockquote' => 'إقتباس',
            'bold' => 'عريض',
            'bullet_list' => 'قائمة نقطية',
            'clear_formatting' => 'مسح التنسيق',
            'code' => 'كود',
            'code_block' => 'نص برمجي',
            'custom_blocks' => 'الكتل المخصصة',
            'details' => 'التفاصيل',
            'h1' => 'عنوان',
            'h2' => 'عنوان رئيسي',
            'h3' => 'عنوان فرعي',
            'grid' => 'شبكة',
            'grid_delete' => 'حذف الشبكة',
            'highlight' => 'تظليل',
            'horizontal_rule' => 'خط أفقي',
            'italic' => 'مائل',
            'lead' => 'نص بارز',
            'link' => 'رابط تشعبي',
            'merge_tags' => 'حقول الدمج',
            'ordered_list' => 'قائمة رقمية',
            'redo' => 'إعادة',
            'small' => 'نص صغير',
            'strike' => 'خط في المنتصف',
            'subscript' => 'نص سفلي',
            'superscript' => 'نص علوي',
            'table' => 'جدول',
            'table_delete' => 'حذف الجدول',
            'table_add_column_before' => 'إضافة عمود قبل',
            'table_add_column_after' => 'إضافة عمود بعد',
            'table_delete_column' => 'حذف العمود',
            'table_add_row_before' => 'إضافة صف قبل',
            'table_add_row_after' => 'إضافة صف بعد',
            'table_delete_row' => 'حذف الصف',
            'table_merge_cells' => 'دمج الخلايا',
            'table_split_cell' => 'فصل الخلايا',
            'table_toggle_header_row' => 'إظهار/إخفاء الترويسة',
            'text_color' => 'لون النص',
            'underline' => 'خط اسفل النص',
            'undo' => 'تراجع',
        ],

        'uploading_file_message' => 'جاري رفع الملف...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'إضافة',

                'modal' => [

                    'heading' => 'إضافة',

                    'actions' => [

                        'create' => [
                            'label' => 'إضافة',
                        ],

                        'create_another' => [
                            'label' => 'إضافة وبدء إضافة المزيد',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'تعديل',

                'modal' => [

                    'heading' => 'تحرير',

                    'actions' => [

                        'save' => [
                            'label' => 'حفظ',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'نعم',
            'false' => 'لا',
        ],

        'loading_message' => 'تحميل...',

        'max_items_message' => 'يمكنك اختيار :count فقط.',

        'no_options_message' => 'لا توجد خيارات متاحة.',

        'no_search_results_message' => 'لا توجد خيارات تطابق بحثك.',

        'placeholder' => 'اختر',

        'searching_message' => 'جاري البحث...',

        'search_prompt' => 'ابدأ بالكتابة للبحث...',

    ],

    'tags_input' => [
        'placeholder' => 'كلمة مفتاحية جديدة',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'نسخ',
                'message' => 'تم النسخ',
            ],

            'hide_password' => [
                'label' => 'إخفاء كلمة المرور',
            ],

            'show_password' => [
                'label' => 'عرض كلمة المرور',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'نعم',
            'false' => 'لا',
        ],

    ],

];

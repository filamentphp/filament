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

        'toolbar_buttons' => [
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

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'ربط',
                    'unlink' => 'فصل',
                ],

                'label' => 'عنوان url',

                'placeholder' => 'أدخل عنوان url',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'إرفاق ملفات',
            'blockquote' => 'إقتباس',
            'bold' => 'عريض',
            'bullet_list' => 'قائمة نقطية',
            'code_block' => 'نص برمجي',
            'h1' => 'عنوان',
            'h2' => 'عنوان رئيسي',
            'h3' => 'عنوان فرعي',
            'italic' => 'مائل',
            'link' => 'رابط تشعبي',
            'ordered_list' => 'قائمة رقمية',
            'redo' => 'إعادة',
            'strike' => 'خط في المنتصف',
            'underline' => 'خط اسفل النص',
            'undo' => 'تراجع',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

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

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'الخطوة السابقة',
            ],

            'next_step' => [
                'label' => 'الخطوة التالية',
            ],

        ],

    ],

];

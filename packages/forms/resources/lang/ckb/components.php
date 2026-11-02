<?php

return [

    'builder' => [

        'reordering' => [
            'item' => 'بلۆک',
            'position' => ':label، شوێنی :position لە :count',
            'moved' => ':label گوازرایەوە بۆ شوێنی :position لە :count',
        ],

        'actions' => [

            'clone' => [
                'label' => 'لەبەرگرتنەە',
            ],

            'add' => [

                'label' => 'زیادکردن بۆ :label',

                'modal' => [

                    'heading' => 'زیادکردن بۆ :label',

                    'actions' => [

                        'add' => [
                            'label' => 'زیادکردن',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'زیادکردن لەنێوان بلۆکەکان',

                'modal' => [

                    'heading' => 'زیادکردن بۆ :label',

                    'actions' => [

                        'add' => [
                            'label' => 'زیادکردن',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'سڕینەوە',
            ],

            'edit' => [

                'label' => 'دەستکاریکردن',

                'modal' => [

                    'heading' => 'دەستکاریکردنی بلۆک',

                    'actions' => [

                        'save' => [
                            'label' => 'پاشەکەوتکردنی بلۆکەکان',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'جوڵاندن',
            ],

            'move_down' => [
                'label' => 'جوڵان بۆ خوارەوە',
            ],

            'move_up' => [
                'label' => 'جوڵان بۆ سەرەوە',
            ],

            'collapse' => [
                'label' => 'داخستن',
            ],

            'expand' => [
                'label' => 'کردنەوە',
            ],

            'collapse_all' => [
                'label' => 'داخستنی هەموو',
            ],

            'expand_all' => [
                'label' => 'کردنەوەی هەموو',
            ],

        ],

        'block_picker' => [

            'no_search_results_message' => 'هیچ بلۆکێک لەگەڵ گەڕانەکەت ناگونجێت.',

            'search_prompt' => 'گەڕان لە بلۆکەکان',

        ],

    ],

    'checkbox_list' => [

        'required_description' => 'لانیکەم یەک هەڵبژاردە هەڵبژێرە.',

        'actions' => [

            'deselect_all' => [
                'label' => 'هەڵنەبژاردنی هەموو',
            ],

            'select_all' => [
                'label' => 'هەڵبژاردنی هەموو',
            ],

        ],

    ],

    'color_picker' => [

        'panel_label' => 'هەڵبژێری ڕەنگ',

    ],

    'date_time_picker' => [

        'month_select' => [
            'label' => 'مانگ',
        ],

        'year_input' => [
            'label' => 'ساڵ',
        ],

        'hour_input' => [
            'label' => 'کاتژمێر',
        ],

        'minute_input' => [
            'label' => 'خولەک',
        ],

        'second_input' => [
            'label' => 'چرکە',
        ],

    ],

    'file_upload' => [

        'placeholder' => 'هیچ پەڕگەیەک بارنەکراوە',

        'actions' => [

            'download' => [
                'label' => 'داگرتن',
            ],

            'open' => [
                'label' => 'کردنەوە لە تابێکی نوێدا',
            ],

        ],

        'editor' => [

            'label' => 'دەستکاریکەری وێنە',

            'actions' => [

                'cancel' => [
                    'label' => 'پاشگەزبوونەوە',
                ],

                'drag_crop' => [
                    'label' => 'دۆخی ڕاکێشان "بڕین"',
                ],

                'drag_move' => [
                    'label' => 'دۆخی ڕاکێشان "جوڵاندن"',
                ],

                'flip_horizontal' => [
                    'label' => 'هەڵگەڕاندنەوەی وێنە بە ئاسۆیی',
                ],

                'flip_vertical' => [
                    'label' => 'هەڵگەڕاندنەوەی وێنە بە ستوونی',
                ],

                'move_down' => [
                    'label' => 'جوڵاندنی وێنە بۆ خوارەوە',
                ],

                'move_left' => [
                    'label' => 'جوڵاندنی وێنە بۆلای چەپ',
                ],

                'move_right' => [
                    'label' => 'جوڵاندنی وێنە بۆلای ڕاست',
                ],

                'move_up' => [
                    'label' => 'جوڵاندنی وێنە بۆ سەرەوە',
                ],

                'reset' => [
                    'label' => 'هەڵوەشاندنەوە',
                ],

                'rotate_left' => [
                    'label' => 'وێنەکە بسووڕێنە بۆ لای چەپ',
                ],

                'rotate_right' => [
                    'label' => 'وێنەکە بسووڕێنە بۆ لای ڕاست',
                ],

                'set_aspect_ratio' => [
                    'label' => 'ڕێژەی ڕووبەر بۆ :ratio',
                ],

                'save' => [
                    'label' => 'پاشەکەوتکردن',
                ],

                'zoom_100' => [
                    'label' => 'نزیککردنەوەی وێنە بۆ 100%',
                ],

                'zoom_in' => [
                    'label' => 'نزیککردنەوە',
                ],

                'zoom_out' => [
                    'label' => 'دوورخستنەوە',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'بەرزی',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'خولانەوە',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'پانی',
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

                'label' => 'ڕێژەی ڕووبەرەکان',

                'no_fixed' => [
                    'label' => 'خۆڕایی',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'دەستکاریکردنی پەڕگەی SVG ڕێگەپێدراو نییە، چونکە لەوانەیە ببێتە هۆی لەدەستدانی کوالیتی کاتێک بەقەبارە دەگۆڕدرێت.\n ئایا دڵنیایت کە دەتەوێت بەردەوام بیت؟',
                    'disabled' => 'دەستکاریکردنی پەڕگەی SVG ناکرێت، چونکە لەوانەیە ببێتە هۆی لەدەستدانی کوالیتی کاتێک بەقەبارە دەگۆڕدرێت.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'زیادکردنی ڕیز',
            ],

            'delete' => [
                'label' => 'سڕینەوەی ڕیز',
            ],

            'move_down' => [
                'label' => 'جوڵان بۆ خوارەوە',
            ],

            'move_up' => [
                'label' => 'جوڵان بۆ سەرەوە',
            ],

            'reorder' => [
                'label' => 'ڕیزکردنەوەی ڕیز',
            ],

        ],

        'columns' => [

            'actions' => [
                'label' => 'کردارەکان',
            ],

            'reorder' => [
                'label' => 'ڕیزکردنەوە',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'کلیل',
            ],

            'value' => [
                'label' => 'بەها',
            ],

        ],

        'reordering' => [
            'row' => 'ڕیز',
            'position' => ':label، شوێنی :position لە :count',
            'moved' => ':label گوازرایەوە بۆ شوێنی :position لە :count',
        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'پەڕگە بارکراوەکان دەبێت لەم جۆرانە بن: :values.',

        'file_attachments_max_size_message' => 'پەڕگە بارکراوەکان نابێت لە :max کیلۆبایت گەورەتر بن.',

        'tools' => [
            'attach_files' => 'هاوپێچکردنی پەڕگەکان',
            'blockquote' => 'بلۆکی وتەی وەرگیراو',
            'bold' => 'تۆخ',
            'bullet_list' => 'لیستی خاڵدار',
            'code_block' => 'بلۆکی کۆد',
            'heading' => 'سەردێڕ',
            'italic' => 'لار',
            'link' => 'بەستەر',
            'ordered_list' => 'لیستی ژمارەکراو',
            'redo' => 'دووبارەکردنەوە',
            'strike' => 'هێڵ بەسەرداهێنان',
            'table' => 'خشتە',
            'undo' => 'پاشەکشە',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'هەڵبژاردن',

                'actions' => [

                    'select' => [
                        'label' => 'هەڵبژاردن',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'بەڵێ',
            'false' => 'نەخێر',
        ],

    ],

    'repeater' => [

        'reordering' => [
            'item' => 'دانە',
            'position' => ':label، شوێنی :position لە :count',
            'moved' => ':label گوازرایەوە بۆ شوێنی :position لە :count',
        ],

        'columns' => [

            'actions' => [
                'label' => 'کردارەکان',
            ],

            'reorder' => [
                'label' => 'ڕیزکردنەوە',
            ],

        ],

        'actions' => [

            'add' => [
                'label' => 'زیادکردن بۆ :label',
            ],

            'add_between' => [
                'label' => 'دانان لەنێوان',
            ],

            'delete' => [
                'label' => 'سڕینەوە',
            ],

            'clone' => [
                'label' => 'لەبەرگرتنەوە',
            ],

            'reorder' => [
                'label' => 'جوڵاندن',
            ],

            'move_down' => [
                'label' => 'جوڵان بۆ خوارەوە',
            ],

            'move_up' => [
                'label' => 'جوڵان بۆ سەرەوە',
            ],

            'collapse' => [
                'label' => 'داخستن',
            ],

            'expand' => [
                'label' => 'کردنەوە',
            ],

            'collapse_all' => [
                'label' => 'داخستنی هەمووی',
            ],

            'expand_all' => [
                'label' => 'کردنەوەی هەمووی',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'بارکردنی پەڕگە',

                'modal' => [

                    'heading' => 'بارکردنی پەڕگە',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'پەڕگە',
                                'existing' => 'شوێنگرتنەوەی پەڕگە',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'دەقی جێگرەوە',
                                'existing' => 'گۆڕینی دەقی جێگرەوە',
                            ],

                        ],

                    ],

                ],

            ],

            'close_panel' => [
                'label' => 'داخستنی پانێڵ',
            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'دانان',
                        ],

                        'save' => [
                            'label' => 'پاشەکەوتکردن',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'تۆڕ',

                'modal' => [

                    'heading' => 'تۆڕ',

                    'form' => [

                        'preset' => [

                            'label' => 'شێوازی ئامادە',

                            'placeholder' => 'هیچ',

                            'options' => [
                                'two' => 'دوو',
                                'three' => 'سێ',
                                'four' => 'چوار',
                                'five' => 'پێنج',
                                'two_start_third' => 'دوو (سێیەک لە سەرەتا)',
                                'two_end_third' => 'دوو (سێیەک لە کۆتایی)',
                                'two_start_fourth' => 'دوو (چارەکێک لە سەرەتا)',
                                'two_end_fourth' => 'دوو (چارەکێک لە کۆتایی)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'ستوونەکان',
                        ],

                        'from_breakpoint' => [

                            'label' => 'لە قەبارەی شاشەوە',

                            'options' => [
                                'default' => 'هەموو',
                                'sm' => 'بچووک',
                                'md' => 'مامناوەند',
                                'lg' => 'گەورە',
                                'xl' => 'زۆر گەورە',
                                '2xl' => 'زۆر زۆر گەورە',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'دوو ستوونی ناهاوتا',
                        ],

                        'start_span' => [
                            'label' => 'پانیی ستوونی سەرەتا',
                        ],

                        'end_span' => [
                            'label' => 'پانیی ستوونی کۆتایی',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'دەستکاریکردن',

                'modal' => [

                    'heading' => 'بەستەر',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'کردنەوە لە تابێکی تردا',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'ڕەنگی دەق',

                'modal' => [

                    'heading' => 'ڕەنگی دەق',

                    'form' => [

                        'color' => [
                            'label' => 'ڕەنگ',

                            'options' => [
                                'slate' => 'خۆڵەمێشیی شینباو',
                                'gray' => 'خۆڵەمێشی',
                                'zinc' => 'زینکی',
                                'neutral' => 'بێلایەن',
                                'stone' => 'بەردین',
                                'mauve' => 'مۆڤ',
                                'olive' => 'زەیتوونی',
                                'mist' => 'تەماوی',
                                'taupe' => 'قاوەیی خۆڵەمێشی',
                                'red' => 'سوور',
                                'orange' => 'پرتەقاڵی',
                                'amber' => 'کارەبایی',
                                'yellow' => 'زەرد',
                                'lime' => 'لیمۆیی',
                                'green' => 'سەوز',
                                'emerald' => 'زومڕوودی',
                                'teal' => 'شینی سەوزباو',
                                'cyan' => 'سیان',
                                'sky' => 'ئاسمانی',
                                'blue' => 'شین',
                                'indigo' => 'نیلی',
                                'violet' => 'وەنەوشەیی',
                                'purple' => 'مۆر',
                                'fuchsia' => 'فووشیا',
                                'pink' => 'پەمەیی',
                                'rose' => 'گوڵەباخی',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'ڕەنگی دڵخواز',
                        ],

                    ],

                ],

            ],

        ],

        'custom_blocks' => [

            'actions' => [

                'delete' => [
                    'label' => 'سڕینەوەی بلۆک',
                ],

                'edit' => [
                    'label' => 'دەستکاریکردنی بلۆک',
                ],

            ],

            'no_search_results_message' => 'هیچ بلۆکێک لەگەڵ گەڕانەکەت ناگونجێت.',

            'search_label' => 'گەڕان لە بلۆکەکان',

            'search_prompt' => 'گەڕان لە بلۆکەکان',

        ],

        'file_attachments_accepted_file_types_message' => 'پەڕگە بارکراوەکان دەبێت لەم جۆرانە بن: :values.',

        'file_attachments_max_size_message' => 'پەڕگە بارکراوەکان نابێت لە :max کیلۆبایت گەورەتر بن.',

        'no_merge_tag_search_results_message' => 'هیچ ئەنجامێکی تاگی تێکەڵکردن نییە.',

        'mentions' => [
            'no_options_message' => 'هیچ هەڵبژاردەیەک بەردەست نییە.',
            'no_search_results_message' => 'هیچ ئەنجامێک لەگەڵ گەڕانەکەت ناگونجێت.',
            'search_prompt' => 'دەست بکە بە نووسین بۆ گەڕان',
            'searching_message' => 'گەڕان...',
        ],

        'toolbar' => [
            'label' => 'شریتی ئامرازەکانی دەستکاریکەر',
        ],

        'tools' => [
            'align_center' => 'لاگرتنی ناوەڕاست',
            'align_end' => 'لاگرتنی کۆتایی',
            'align_justify' => 'لاگرتنی هاوڕێک',
            'align_start' => 'لاگرتنی سەرەتا',
            'attach_files' => 'هاوپێچکردنی پەڕگەکان',
            'blockquote' => 'بلۆکی وتەی وەرگیراو',
            'bold' => 'تۆخ',
            'bullet_list' => 'لیستی خاڵدار',
            'clear_formatting' => 'پاککردنەوەی شێواز',
            'code' => 'کۆد',
            'code_block' => 'بلۆکی کۆد',
            'custom_blocks' => 'بلۆکەکان',
            'details' => 'وەردەکارییەکان',
            'h1' => 'ناونشان',
            'h2' => 'سەردێڕ',
            'h3' => 'ژێرسەردێڕ',
            'h4' => 'سەردێڕی 4',
            'h5' => 'سەردێڕی 5',
            'h6' => 'سەردێڕی 6',
            'grid' => 'تۆڕ',
            'grid_delete' => 'سڕینەوەی تۆڕ',
            'highlight' => 'بەرچاوکردن',
            'horizontal_rule' => 'یاسای ئاسۆیی',
            'italic' => 'لار',
            'lead' => 'دەقی سەرەکی',
            'link' => 'بەستەر',
            'merge_tags' => 'تاگەکان تێکەڵ بکە',
            'ordered_list' => 'لیستی ژمارەکراو',
            'paragraph' => 'بڕگە',
            'redo' => 'دووبارەکردنەوە',
            'small' => 'دەقی بچووک',
            'strike' => 'هێڵ بەسەرداهێنان',
            'subscript' => 'ژێرنووس',
            'superscript' => 'سەرنووس',
            'table' => 'خشتە',
            'table_delete' => 'سڕینەوەی خشتە',
            'table_add_column_before' => 'زیادکردنی ستوون لەپێشەوە',
            'table_add_column_after' => 'زیادکردنی ستوون لەدواوە',
            'table_delete_column' => 'سڕینەوەی ستوون',
            'table_add_row_before' => 'زیادکردنی ڕیز لەسەرەوە',
            'table_add_row_after' => 'زیادکردنی ڕیز لەخوارەوە',
            'table_delete_row' => 'سڕینەوەی ڕیز',
            'table_merge_cells' => 'تێکەڵکردنی خانەکان',
            'table_split_cell' => 'جیاکردنەوەی خانە',
            'table_toggle_header_row' => 'گۆڕینی سەرەوەی ڕیز',
            'table_toggle_header_cell' => 'گۆڕینی خانەی سەرەوە',
            'text_color' => 'ڕەنگی دەق',
            'underline' => 'هێلبەژێرداهاتوو',
            'undo' => 'پاشەکشە',
        ],

        'uploading_file_message' => 'بارکردنی پەڕگە...',

    ],

    'select' => [

        'actions' => [

            'clear' => [
                'label' => 'پاککردنەوەی هەڵبژاردن',
            ],

            'create_option' => [

                'label' => 'زیادکردن',

                'modal' => [

                    'heading' => 'زیادکردن',

                    'actions' => [

                        'create' => [
                            'label' => 'زیادکردن',
                        ],

                        'create_another' => [
                            'label' => 'زیادکردن & زیادکردنی دانەیەکی تر',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'دەستکاریکردن',

                'modal' => [

                    'heading' => 'دەستکاریکردن',

                    'actions' => [

                        'save' => [
                            'label' => 'پاشەکەوتکردن',
                        ],

                    ],

                ],

            ],

            'remove_option' => [
                'label' => 'لابردنی :label',
            ],

        ],

        'boolean' => [
            'true' => 'بەڵێ',
            'false' => 'نەخێر',
        ],

        'error_message' => 'هەڵبژاردەکان بارنەکران. تکایە دووبارە هەوڵ بدەرەوە.',

        'loading_message' => 'بارکردن...',

        'max_items_message' => 'دەتوانرێت تەنها :count هەڵبژێردرێت.',

        'no_options_message' => 'هیچ هەڵبژاردەیەک بەردەست نییە.',

        'no_search_results_message' => 'هیچ شتێک هاوشێوەی گەڕانەکەت نیە.',

        'placeholder' => 'هەڵبژێرە',

        'required_description' => 'هەڵبژاردەیەک هەڵبژێرە.',

        'searching_message' => 'گەڕان...',

        'search_label' => 'گەڕان',

        'search_prompt' => 'دەستبکە بە گەڕان...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'سڕینەوە',
            ],

        ],

        'placeholder' => 'تاگی نوێ',

        'tag_added' => 'زیادکرا: :tag',

        'tag_removed' => 'لابرا: :tag',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'لەبەرگرتنەوە',
                'message' => 'لەبەرگیرایەوە',
            ],

            'hide_password' => [
                'label' => 'شاردنەوەی تێپەڕەوشە',
            ],

            'show_password' => [
                'label' => 'پیشاندانی تێپەڕەوشە',
            ],

        ],

    ],

    'toggle_buttons' => [

        'required_description' => 'لانیکەم یەک هەڵبژاردە هەڵبژێرە.',

        'boolean' => [
            'true' => 'بەڵێ',
            'false' => 'نەخێر',
        ],

    ],

];

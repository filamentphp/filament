<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'ក្លូន',
            ],

            'add' => [
                'label' => 'បន្ថែម​លើ :label',

                'modal' => [

                    'heading' => 'បន្ថែម​លើ :label',

                    'actions' => [

                        'add' => [
                            'label' => 'បន្ថែម',
                        ],

                    ],

                ],
            ],

            'add_between' => [
                'label' => 'បញ្ចូលរវាងប្លុក',

                'modal' => [

                    'heading' => 'បន្ថែម​លើ :label',

                    'actions' => [

                        'add' => [
                            'label' => 'បន្ថែម',
                        ],

                    ],

                ],
            ],

            'delete' => [
                'label' => 'លុប',
            ],

            'edit' => [

                'label' => 'កែសម្រួល',

                'modal' => [

                    'heading' => 'កែសម្រួលប្លុក',

                    'actions' => [

                        'save' => [
                            'label' => 'រក្សាទុកការផ្លាស់ប្តូរ',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'ផ្លាស់ទី',
            ],

            'move_down' => [
                'label' => 'ទៅ​ក្រោម',
            ],

            'move_up' => [
                'label' => 'ផ្លាស់ទីឡើងលើ',
            ],

            'collapse' => [
                'label' => 'ដួលរលំ',
            ],

            'expand' => [
                'label' => 'ពង្រីក',
            ],

            'collapse_all' => [
                'label' => 'ដួលរលំទាំងអស់',
            ],

            'expand_all' => [
                'label' => 'ពង្រីកទាំងអស់',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'ដកការជ្រើសរើសទាំងអស់',
            ],

            'select_all' => [
                'label' => 'ជ្រើសរើសទាំងអស់',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'លុបចោល',
                ],

                'drag_crop' => [
                    'label' => 'របៀបអូសដោយ "ច្រឹប"',
                ],

                'drag_move' => [
                    'label' => 'របៀបអូសដោយ "ផ្លាស់ទី"',
                ],

                'flip_horizontal' => [
                    'label' => 'ត្រឡប់​រូបភាពតាមជួរដេក',
                ],

                'flip_vertical' => [
                    'label' => 'ត្រឡប់​រូបភាពតាមជួរឈរ',
                ],

                'move_down' => [
                    'label' => 'រំកិលរូបភាពចុះក្រោម',
                ],

                'move_left' => [
                    'label' => 'រំកិលរូបភាពទៅឆ្វេង',
                ],

                'move_right' => [
                    'label' => 'រំកិលរូបភាពទៅស្តាំ',
                ],

                'move_up' => [
                    'label' => 'រំកិលរូបភាពទៅលើ',
                ],

                'reset' => [
                    'label' => 'កំណត់ឡើងវិញ',
                ],

                'rotate_left' => [
                    'label' => 'បង្វិលរូបភាពទៅឆ្វេង',
                ],

                'rotate_right' => [
                    'label' => 'បង្វិលរូបភាពទៅស្តាំ',
                ],

                'set_aspect_ratio' => [
                    'label' => 'កំណត់សមាមាត្រទំហំទៅ :ratio',
                ],

                'save' => [
                    'label' => 'រក្សាទុក',
                ],

                'zoom_100' => [
                    'label' => 'ប្តូរទំហំរូបភាពទៅ១០០%',
                ],

                'zoom_in' => [
                    'label' => 'ពង្រីក',
                ],

                'zoom_out' => [
                    'label' => 'បង្រួម',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'កំពស់',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'ការបង្វិល',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'ទទឹង',
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

                'label' => 'សមាមាត្រទំហំ',

                'no_fixed' => [
                    'label' => 'មិនកំណត់',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'ការកែសម្រួលឯកសារ SVG មិនត្រូវបានណែនាំទេ ព្រោះវាអាចបណ្តាលឱ្យបាត់បង់គុណភាពនៅពេលធ្វើមាត្រដ្ឋាន។\n តើអ្នកប្រាកដថាចង់បន្តទេ?',
                    'disabled' => 'ការកែសម្រួលឯកសារ SVG ត្រូវបានបិទព្រោះវាអាចបណ្តាលឱ្យបាត់បង់គុណភាពនៅពេលធ្វើមាត្រដ្ឋាន។',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'បន្ថែមជួរ',
            ],

            'delete' => [
                'label' => 'លុបជួរ',
            ],

            'reorder' => [
                'label' => 'តម្រៀបជួរម្តងទៀត',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'គន្លឺះ',
            ],

            'value' => [
                'label' => 'តម្លៃ',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'ភ្ជាប់ឯកសារ',
            'blockquote' => 'ប្លុកសម្រង់',
            'bold' => 'ដិត',
            'bullet_list' => 'បញ្ជីគ្រាប់',
            'code_block' => 'ប្លុកកូដ',
            'heading' => 'Heading',
            'italic' => 'ទ្រេត',
            'link' => 'តំណភ្ជាប់',
            'ordered_list' => 'បញ្ជីលេខ',
            'redo' => 'ធ្វើឡើងវិញ',
            'strike' => 'ការវាយឆ្មក់',
            'table' => 'តុ',
            'undo' => 'មិនធ្វើវិញ',
        ],

        'file_attachments_accepted_file_types_message' => 'ប្រភេទឯកសារដែលត្រូវបានអនុញ្ញាត៖ :values.',

        'file_attachments_max_size_message' => 'ទំហំឯកសារមិនត្រូវលើសពី :max kilobytes ឡើយ។',

    ],

    'modal_table_select' => [
        'actions' => [
            'select' => [
                'label' => 'ជ្រើសរើស',
                'actions' => [
                    'select' => [
                        'label' => 'ជ្រើសរើស',
                    ],
                ],
            ],
        ],
    ],

    'radio' => [

        'boolean' => [
            'true' => 'បាទ',
            'false' => 'ទេ',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'បន្ថែម​លើ :label',
            ],

            'add_between' => [
                'label' => 'បញ្ចូលរវាង',
            ],

            'delete' => [
                'label' => 'លុប',
            ],

            'clone' => [
                'label' => 'ក្លូន',
            ],

            'reorder' => [
                'label' => 'ផ្លាស់ទី',
            ],

            'move_down' => [
                'label' => 'ទៅ​ក្រោម',
            ],

            'move_up' => [
                'label' => 'ផ្លាស់ទីឡើងលើ',
            ],

            'collapse' => [
                'label' => 'ដួលរលំ',
            ],

            'expand' => [
                'label' => 'ពង្រីក',
            ],

            'collapse_all' => [
                'label' => 'ដួលរលំទាំងអស់',
            ],

            'expand_all' => [
                'label' => 'ពង្រីកទាំងអស់',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [
                'label' => 'ភ្ជាប់ឯកសារ',

                'modal' => [

                    'heading' => 'ភ្ជាប់ឯកសារ',

                    'form' => [

                        'file' => [
                            'label' => [
                                'new' => 'ឯកសារ',
                                'existing' => 'ប្ដូរឯកសារ',
                            ],
                        ],

                        'alt' => [
                            'label' => [
                                'new' => 'អត្ថបទជំនួស (Alt text)',
                                'existing' => 'កែប្រែអត្ថបទជំនួស',
                            ],
                        ],

                    ],

                ],
            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'បញ្ចូល',
                        ],

                        'save' => [
                            'label' => 'រក្សាទុក',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'ក្រឡាចត្រង្គ (Grid)',

                'modal' => [

                    'heading' => 'ក្រឡាចត្រង្គ',

                    'form' => [

                        'preset' => [
                            'label' => 'ការកំណត់ទុកជាមុន (Preset)',
                            'placeholder' => 'គ្មាន',
                            'options' => [
                                'two' => 'ពីរ',
                                'three' => 'បី',
                                'four' => 'បួន',
                                'five' => 'ប្រាំ',
                                'two_start_third' => 'ពីរ (ចាប់ផ្តើមមួយភាគបី)',
                                'two_end_third' => 'ពីរ (បញ្ចប់មួយភាគបី)',
                                'two_start_fourth' => 'ពីរ (ចាប់ផ្តើមមួយភាគបួន)',
                                'two_end_fourth' => 'ពីរ (បញ្ចប់មួយភាគបួន)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'ជួរឈរ',
                        ],

                        'from_breakpoint' => [
                            'label' => 'ពីចំណុចបំបែក (Breakpoint)',
                            'options' => [
                                'default' => 'ទាំងអស់',
                                'sm' => 'តូច',
                                'md' => 'មធ្យម',
                                'lg' => 'ធំ',
                                'xl' => 'ធំបំផុត',
                                '2xl' => 'ពីរដងធំបំផុត',
                            ],
                        ],

                        'is_asymmetric' => [
                            'label' => 'ពីរជួរឈរមិនស្មើគ្នា',
                        ],

                        'start_span' => [
                            'label' => 'ចម្ងាយចាប់ផ្តើម',
                        ],

                        'end_span' => [
                            'label' => 'ចម្ងាយបញ្ចប់',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'តំណភ្ជាប់',

                'modal' => [

                    'heading' => 'តំណភ្ជាប់',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'បើកក្នុងផ្ទាំងថ្មី',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'ពណ៌អត្ថបទ',

                'modal' => [

                    'heading' => 'ពណ៌អត្ថបទ',

                    'form' => [

                        'color' => [
                            'label' => 'ពណ៌',
                        ],

                        'custom_color' => [
                            'label' => 'ពណ៌តាមចំណូលចិត្ត',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'ប្រភេទឯកសារដែលត្រូវបានអនុញ្ញាត៖ :values.',

        'file_attachments_max_size_message' => 'ទំហំឯកសារមិនត្រូវលើសពី :max kilobytes ឡើយ។',

        'no_merge_tag_search_results_message' => 'រកមិនឃើញលទ្ធផលស្លាកច្របាច់បញ្ចូលគ្នា (Merge tag) ទេ។',

        'mentions' => [
            'no_options_message' => 'គ្មានជម្រើស។',
            'no_search_results_message' => 'រកមិនឃើញលទ្ធផលត្រូវនឹងការស្វែងរករបស់អ្នកទេ។',
            'search_prompt' => 'ចាប់ផ្តើមវាយដើម្បីស្វែងរក...',
            'searching_message' => 'កំពុងស្វែងរក...',
        ],

        'tools' => [
            'align_center' => 'តម្រឹមចំកណ្តាល',
            'align_end' => 'តម្រឹមខាងចុង',
            'align_justify' => 'តម្រឹមសងខាង',
            'align_start' => 'តម្រឹមខាងដើម',
            'attach_files' => 'ភ្ជាប់ឯកសារ',
            'blockquote' => 'ប្លុកសម្រង់',
            'bold' => 'ដិត',
            'bullet_list' => 'បញ្ជីគ្រាប់',
            'clear_formatting' => 'សម្អាតទម្រង់ (Clear formatting)',
            'code' => 'កូដ',
            'code_block' => 'ប្លុកកូដ',
            'custom_blocks' => 'ប្លុក',
            'details' => 'ព័ត៌មានលម្អិត',
            'h1' => 'ចំណងជើង',
            'h2' => 'ក្បាល',
            'h3' => 'ចំណងជើងរង',
            'grid' => 'ក្រឡាចត្រង្គ',
            'grid_delete' => 'លុបក្រឡាចត្រង្គ',
            'highlight' => 'រំលេច (Highlight)',
            'horizontal_rule' => 'បន្ទាត់ដេក',
            'italic' => 'ទ្រេត',
            'lead' => 'អត្ថបទដឹកនាំ (Lead text)',
            'link' => 'តំណភ្ជាប់',
            'merge_tags' => 'ស្លាកច្របាច់បញ្ចូលគ្នា (Merge tags)',
            'ordered_list' => 'បញ្ជីលេខ',
            'redo' => 'ធ្វើឡើងវិញ',
            'small' => 'អត្ថបទតូច',
            'strike' => 'ការវាយឆ្មក់',
            'subscript' => 'អក្សរក្រោម (Subscript)',
            'superscript' => 'អក្សរលើ (Superscript)',
            'table' => 'តារាង',
            'table_delete' => 'លុបតារាង',
            'table_add_column_before' => 'បន្ថែមជួរឈរខាងមុខ',
            'table_add_column_after' => 'បន្ថែមជួរឈរខាងក្រោយ',
            'table_delete_column' => 'លុបជួរឈរ',
            'table_add_row_before' => 'បន្ថែមជួរដេកខាងលើ',
            'table_add_row_after' => 'បន្ថែមជួរដេកខាងក្រោម',
            'table_delete_row' => 'លុបជួរដេក',
            'table_merge_cells' => 'ច្របាច់បញ្ចូលក្រឡា',
            'table_split_cell' => 'បំបែកក្រឡា',
            'table_toggle_header_row' => 'បិទ/បើកជួរដេកចំណងជើង',
            'table_toggle_header_cell' => 'បិទ/បើកក្រឡាចំណងជើង',
            'text_color' => 'ពណ៌អត្ថបទ',
            'underline' => 'គូសបន្ទាត់ពីក្រោម',
            'undo' => 'មិនធ្វើវិញ',
        ],

        'uploading_file_message' => 'កំពុងផ្ញើឯកសារ...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'បង្កើត',

                'modal' => [

                    'heading' => 'បង្កើត',

                    'actions' => [

                        'create' => [
                            'label' => 'បង្កើត',
                        ],

                        'create_another' => [
                            'label' => 'បង្កើត & បង្កើតឡើងវិញ',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'កែសម្រួល',

                'modal' => [

                    'heading' => 'កែសម្រួល',

                    'actions' => [

                        'save' => [
                            'label' => 'រក្សាទុក',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'បាទ',
            'false' => 'ទេ',
        ],

        'loading_message' => 'កំពុងផ្ទុក...',

        'max_items_message' => 'តែប៉ុណ្ណោះ :count អាចត្រូវបានជ្រើសរើស.',

        'no_search_results_message' => 'មិនមានជម្រើសត្រូវជាមួយការស្វែងរក.',

        'no_options_message' => 'គ្មានជម្រើស។',

        'placeholder' => 'ជ្រើសរើសជម្រើស',

        'searching_message' => 'ស្វែងរក...',

        'search_prompt' => 'ចាប់ផ្តើមសសេរដើម្បីស្វែងរក...',

    ],

    'tags_input' => [
        'placeholder' => 'ស្លាកថ្មី',
        'actions' => [
            'delete' => [
                'label' => 'លុប',
            ],
        ],
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'លាក់ពាក្យសម្ងាត់',
            ],

            'show_password' => [
                'label' => 'បង្ហាញពាក្យសម្ងាត់',
            ],

            'copy' => [
                'label' => 'ចម្លង',
                'message' => 'បានចម្លង',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'បាទ',
            'false' => 'ទេ',
        ],

    ],

];

<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'অনুলিপি করুন',
            ],

            'add' => [

                'label' => ':label এ যোগ করুন',

                'modal' => [

                    'heading' => ':label এ যোগ করুন',

                    'actions' => [

                        'add' => [
                            'label' => 'যোগ করুন',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'ব্লকগুলোর মাঝে প্রবেশ করান',

                'modal' => [

                    'heading' => ':label এ যোগ করুন',

                    'actions' => [

                        'add' => [
                            'label' => 'যোগ করুন',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'মুছে ফেলুন',
            ],

            'edit' => [

                'label' => 'সম্পাদন',

                'modal' => [

                    'heading' => 'ব্লক সম্পাদন করুন',

                    'actions' => [

                        'save' => [
                            'label' => 'পরিবর্তনগুলো সংরক্ষণ করুন',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'সরান',
            ],

            'move_down' => [
                'label' => 'নিচে সরান',
            ],

            'move_up' => [
                'label' => 'উপরে সরান',
            ],

            'collapse' => [
                'label' => 'ছোট করুন',
            ],

            'expand' => [
                'label' => 'বড় করুন',
            ],

            'collapse_all' => [
                'label' => 'সব ছোট করুন',
            ],

            'expand_all' => [
                'label' => 'সব বড় করুন',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'সবগুলো নির্বাচন বাতিল করুন',
            ],

            'select_all' => [
                'label' => 'সব নির্বাচন করুন',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'বাতিল',
                ],

                'drag_crop' => [
                    'label' => 'ড্র্যাগ মোড "ক্রপ"',
                ],

                'drag_move' => [
                    'label' => 'ড্র্যাগ মোড "মুভ"',
                ],

                'flip_horizontal' => [
                    'label' => 'ছবিটি আড়াআড়ি উল্টান',
                ],

                'flip_vertical' => [
                    'label' => 'ছবিটি লম্বালম্বি উল্টান',
                ],

                'move_down' => [
                    'label' => 'ছবিটি নিচে সরান',
                ],

                'move_left' => [
                    'label' => 'ছবিটি বামে সরান',
                ],

                'move_right' => [
                    'label' => 'ছবিটি ডানে সরান',
                ],

                'move_up' => [
                    'label' => 'ছবিটি উপরে সরান',
                ],

                'reset' => [
                    'label' => 'আগের অবস্থায় ফিরান',
                ],

                'rotate_left' => [
                    'label' => 'ছবিটি বামে ঘুরান',
                ],

                'rotate_right' => [
                    'label' => 'ছবিটি ডানে ঘুরান',
                ],

                'set_aspect_ratio' => [
                    'label' => 'অ্যাসপেক্ট রেশিও :ratio সেট করুন',
                ],

                'save' => [
                    'label' => 'সংরক্ষণ',
                ],

                'zoom_100' => [
                    'label' => 'ছবিটি ১০০% জুম করুন',
                ],

                'zoom_in' => [
                    'label' => 'বড় করুন',
                ],

                'zoom_out' => [
                    'label' => 'ছোট করুন',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'উচ্চতা',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'ঘোরানো',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'প্রস্থ',
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

                'label' => 'অ্যাসপেক্ট রেশিও',

                'no_fixed' => [
                    'label' => 'মুক্ত',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG ফাইল সম্পাদন করা ঠিক নয় কারণ স্কেল করার সময় মান খারাপ হতে পারে।\nআপনি কি নিশ্চিত যে এগিয়ে যেতে চান?',
                    'disabled' => 'SVG ফাইল সম্পাদন করা নিষ্ক্রিয় করা হয়েছে কারণ স্কেল করার সময় মান খারাপ হতে পারে।',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'সারি যোগ করুন',
            ],

            'delete' => [
                'label' => 'সারি মুছে ফেলুন',
            ],

            'reorder' => [
                'label' => 'সারি সাজান',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'কী',
            ],

            'value' => [
                'label' => 'মান',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'আপলোড করা ফাইলগুলো অবশ্যই :values টাইপের হতে হবে।',

        'file_attachments_max_size_message' => 'আপলোড করা ফাইলগুলো অবশ্যই :max কিলোবাইট এর বড় হবে না।',

        'tools' => [
            'attach_files' => 'ফাইল যুক্ত করুন',
            'blockquote' => 'ব্লককোট',
            'bold' => 'বোল্ড',
            'bullet_list' => 'বুলেট তালিকা',
            'code_block' => 'কোড ব্লক',
            'heading' => 'শিরোনাম',
            'italic' => 'তির্যক',
            'link' => 'লিংক',
            'ordered_list' => 'সংখ্যাযুক্ত তালিকা',
            'redo' => 'পুনরায় করুন',
            'strike' => 'কাটা লেখা',
            'table' => 'টেবিল',
            'undo' => 'পূর্বাবস্থায় ফিরুন',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'নির্বাচন',

                'actions' => [

                    'select' => [
                        'label' => 'নির্বাচন করুন',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'হ্যাঁ',
            'false' => 'না',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => ':label এ যোগ করুন',
            ],

            'add_between' => [
                'label' => 'মাঝে প্রবেশ করান',
            ],

            'delete' => [
                'label' => 'মুছে ফেলুন',
            ],

            'clone' => [
                'label' => 'অনুলিপি করুন',
            ],

            'reorder' => [
                'label' => 'সরান',
            ],

            'move_down' => [
                'label' => 'নিচে সরান',
            ],

            'move_up' => [
                'label' => 'উপরে সরান',
            ],

            'collapse' => [
                'label' => 'ছোট করুন',
            ],

            'expand' => [
                'label' => 'বড় করুন',
            ],

            'collapse_all' => [
                'label' => 'সব ছোট করুন',
            ],

            'expand_all' => [
                'label' => 'সব বড় করুন',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'ফাইল আপলোড করুন',

                'modal' => [

                    'heading' => 'ফাইল আপলোড করুন',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'ফাইল',
                                'existing' => 'ফাইল পরিবর্তন করুন',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'বিকল্প লেখা',
                                'existing' => 'বিকল্প লেখা পরিবর্তন করুন',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'প্রবেশ করান',
                        ],

                        'save' => [
                            'label' => 'সংরক্ষণ করুন',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'গ্রিড',

                'modal' => [

                    'heading' => 'গ্রিড',

                    'form' => [

                        'preset' => [

                            'label' => 'প্রিসেট',

                            'placeholder' => 'নেই',

                            'options' => [
                                'two' => 'দুই',
                                'three' => 'তিন',
                                'four' => 'চার',
                                'five' => 'পাঁচ',
                                'two_start_third' => 'দুই (শুরু এক-তৃতীয়াংশ)',
                                'two_end_third' => 'দুই (শেষ এক-তৃতীয়াংশ)',
                                'two_start_fourth' => 'দুই (শুরু এক-চতুর্থাংশ)',
                                'two_end_fourth' => 'দুই (শেষ এক-চতুর্থাংশ)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'কলাম',
                        ],

                        'from_breakpoint' => [

                            'label' => 'ব্রেকপয়েন্ট থেকে',

                            'options' => [
                                'default' => 'সব',
                                'sm' => 'ছোট',
                                'md' => 'মাঝারি',
                                'lg' => 'বড়',
                                'xl' => 'অতিরিক্ত বড়',
                                '2xl' => 'দ্বিগুণ অতিরিক্ত বড়',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'দুটি অসমান কলাম',
                        ],

                        'start_span' => [
                            'label' => 'শুরু স্প্যান',
                        ],

                        'end_span' => [
                            'label' => 'শেষ স্প্যান',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'লিংক',

                'modal' => [

                    'heading' => 'লিংক',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'নতুন ট্যাবে খুলুন',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'লেখার রং',

                'modal' => [

                    'heading' => 'লেখার রং',

                    'form' => [

                        'color' => [
                            'label' => 'রঙ',

                            'options' => [
                                'slate' => 'স্লেট (ধূসর-নীলাভ)',
                                'gray' => 'ধূসর',
                                'zinc' => 'জিঙ্ক (ধূসর)',
                                'neutral' => 'নিউট্রাল',
                                'stone' => 'পাথর রঙ (ধূসর-বাদামি)',
                                'mauve' => 'মভ (হালকা বেগুনি)',
                                'olive' => 'অলিভ (সবুজ-বাদামি)',
                                'mist' => 'কুয়াশা রঙ (হালকা ধূসর)',
                                'taupe' => 'টোপ (ধূসর-বাদামি)',
                                'red' => 'লাল',
                                'orange' => 'কমলা',
                                'amber' => 'অ্যাম্বার (মধু-হলুদ)',
                                'yellow' => 'হলুদ',
                                'lime' => 'লাইম (হালকা সবুজ)',
                                'green' => 'সবুজ',
                                'emerald' => 'পান্না (গাঢ় সবুজ)',
                                'teal' => 'টিল (নীল-সবুজ)',
                                'cyan' => 'সায়ান (আকাশি-নীল)',
                                'sky' => 'আকাশি',
                                'blue' => 'নীল',
                                'indigo' => 'ইন্ডিগো (নীল-বেগুনি)',
                                'violet' => 'ভায়োলেট (বেগুনি)',
                                'purple' => 'বেগুনি',
                                'fuchsia' => 'ফুচিয়া (গাঢ় গোলাপি-বেগুনি)',
                                'pink' => 'গোলাপি',
                                'rose' => 'রোজ (গোলাপ রঙ)',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'নিজস্ব রং',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'আপলোড করা ফাইলগুলো অবশ্যই :values টাইপের হতে হবে।',

        'file_attachments_max_size_message' => 'আপলোড করা ফাইলগুলো অবশ্যই :max কিলোবাইট এর বড় হবে না।',

        'no_merge_tag_search_results_message' => 'কোন মার্জ ট্যাগ পাওয়া যায়নি।',

        'mentions' => [
            'no_options_message' => 'কোন অপশন নেই।',
            'no_search_results_message' => 'আপনার অনুসন্ধানের সাথে মেলে এমন কিছু পাওয়া যায়নি।',
            'search_prompt' => 'অনুসন্ধান শুরু করতে টাইপ করুন...',
            'searching_message' => 'অনুসন্ধান চলছে...',
        ],

        'tools' => [
            'align_center' => 'মাঝখানে সারিবদ্ধ করুন',
            'align_end' => 'শেষে সারিবদ্ধ করুন',
            'align_justify' => 'সমানভাবে সারিবদ্ধ করুন',
            'align_start' => 'শুরুতে সারিবদ্ধ করুন',
            'attach_files' => 'ফাইল সংযুক্ত করুন',
            'blockquote' => 'উক্তি ব্লক',
            'bold' => 'গাঢ় লেখা',
            'bullet_list' => 'বুলেট তালিকা',
            'clear_formatting' => 'ফরম্যাট পরিষ্কার করুন',
            'code' => 'কোড',
            'code_block' => 'কোড ব্লক',
            'custom_blocks' => 'ব্লকসমূহ',
            'details' => 'বিস্তারিত',
            'h1' => 'শিরোনাম',
            'h2' => 'শিরোনাম ২',
            'h3' => 'শিরোনাম ৩',
            'h4' => 'শিরোনাম ৪',
            'h5' => 'শিরোনাম ৫',
            'h6' => 'শিরোনাম ৬',
            'grid' => 'গ্রিড',
            'grid_delete' => 'গ্রিড মুছে ফেলুন',
            'highlight' => 'হাইলাইট',
            'horizontal_rule' => 'আড়াআড়ি রেখা',
            'italic' => 'তির্যক',
            'lead' => 'লিড টেক্সট',
            'link' => 'লিংক',
            'merge_tags' => 'মার্জ ট্যাগ',
            'ordered_list' => 'সংখ্যাযুক্ত তালিকা',
            'paragraph' => 'অনুচ্ছেদ',
            'redo' => 'পুনরায় করুন',
            'small' => 'ছোট লেখা',
            'strike' => 'কাটা লেখা',
            'subscript' => 'নিম্নসূচক',
            'superscript' => 'ঊর্ধ্বসূচক',
            'table' => 'টেবিল',
            'table_delete' => 'টেবিল মুছে ফেলুন',
            'table_add_column_before' => 'আগে কলাম যোগ করুন',
            'table_add_column_after' => 'পরে কলাম যোগ করুন',
            'table_delete_column' => 'কলাম মুছে ফেলুন',
            'table_add_row_before' => 'উপরে সারি যোগ করুন',
            'table_add_row_after' => 'নিচে সারি যোগ করুন',
            'table_delete_row' => 'সারি মুছে ফেলুন',
            'table_merge_cells' => 'সেল একত্রিত করুন',
            'table_split_cell' => 'সেল বিভক্ত করুন',
            'table_toggle_header_row' => 'হেডার সারি চালু/বন্ধ করুন',
            'table_toggle_header_cell' => 'হেডার সেল চালু/বন্ধ করুন',
            'text_color' => 'লেখার কালার',
            'underline' => 'আন্ডারলাইন',
            'undo' => 'পূর্বাবস্থায় ফিরুন',
        ],

        'uploading_file_message' => 'ফাইল আপলোড হচ্ছে...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'তৈরি করুন',

                'modal' => [

                    'heading' => 'তৈরি করুন',

                    'actions' => [

                        'create' => [
                            'label' => 'তৈরি করুন',
                        ],

                        'create_another' => [
                            'label' => 'তৈরি করুন এবং আরেকটি তৈরি করুন',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'সম্পাদন',

                'modal' => [

                    'heading' => 'সম্পাদন করুন',

                    'actions' => [

                        'save' => [
                            'label' => 'সংরক্ষণ করুন',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'হ্যাঁ',
            'false' => 'না',
        ],

        'loading_message' => 'লোড হচ্ছে...',

        'max_items_message' => 'মাত্র :count টি নির্বাচন করা যাবে।',

        'no_options_message' => 'কোন অপশন নেই।',

        'no_search_results_message' => 'আপনার অনুসন্ধানের সাথে মেলে এমন কিছু পাওয়া যায়নি।',

        'placeholder' => 'একটি অপশন নির্বাচন করুন',

        'searching_message' => 'অনুসন্ধান চলছে...',

        'search_prompt' => 'অনুসন্ধান শুরু করতে টাইপ করুন...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'মুছে ফেলুন',
            ],

        ],

        'placeholder' => 'নতুন ট্যাগ',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'কপি করুন',
                'message' => 'কপি হয়েছে',
            ],

            'hide_password' => [
                'label' => 'পাসওয়ার্ড লুকান',
            ],

            'show_password' => [
                'label' => 'পাসওয়ার্ড দেখান',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'হ্যাঁ',
            'false' => 'না',
        ],

    ],

];

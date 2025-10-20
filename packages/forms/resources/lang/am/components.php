<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [

                'label' => 'ድገም',
            ],
            'add' => [

                'label' => 'ወደ :label ጨምር',
                'modal' => [

                    'heading' => 'ወደ :label ጨምር',
                    'actions' => [

                        'add' => [

                            'label' => 'ጨምር',
                        ],
                    ],
                ],
            ],
            'add_between' => [

                'label' => 'በብሎኮች መካከል አስገባ',
                'modal' => [

                    'heading' => 'ወደ :label ጨምር',
                    'actions' => [

                        'add' => [

                            'label' => 'ጨምር',
                        ],
                    ],
                ],
            ],
            'delete' => [

                'label' => 'አጥፋ',
            ],
            'edit' => [

                'label' => 'አድስ',
                'modal' => [

                    'heading' => 'የማደሻ ክልል',
                    'actions' => [

                        'save' => [

                            'label' => 'ለውጦችን አኑር',
                        ],
                    ],
                ],
            ],
            'reorder' => [

                'label' => 'አንቀሳቅስ',
            ],
            'move_down' => [

                'label' => 'ወደ ታች አንቀሳቅስ',
            ],
            'move_up' => [

                'label' => 'ወደ ላይ አንቀሳቅስ',
            ],
            'collapse' => [

                'label' => 'ሰብስብ',
            ],
            'expand' => [

                'label' => 'ዘርጋ',
            ],
            'collapse_all' => [

                'label' => 'ሁሉንም ሰብስብ',
            ],
            'expand_all' => [

                'label' => 'ሁሉንም ዘርጋ',
            ],
        ],
    ],
    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [

                'label' => 'ሁሉንም አለመምረጥ',
            ],
            'select_all' => [

                'label' => 'ሁሉንም መምረጥ',
            ],
        ],
    ],
    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [

                    'label' => 'ይቅር',
                ],
                'drag_crop' => [

                    'label' => 'Drag mode "crop"',
                ],
                'drag_move' => [

                    'label' => 'Drag mode "move"',
                ],
                'flip_horizontal' => [

                    'label' => 'ምስሉን በአግድም ገልብጥ',
                ],
                'flip_vertical' => [

                    'label' => '',
                ],
                'move_down' => [

                    'label' => 'ምስሉን በአቀባዊ ገልብጥ',
                ],
                'move_left' => [

                    'label' => 'ምስል ወደ ግራ ውሰድ',
                ],
                'move_right' => [

                    'label' => 'ምስል ወደ ቀኝ ውሰድ',
                ],
                'move_up' => [

                    'label' => 'ምስል ወደ ላይ ውሰድ',
                ],
                'reset' => [

                    'label' => 'ዳግም አስጀምር',
                ],
                'rotate_left' => [

                    'label' => 'ምስሉን ወደ ግራ አሽከርክር',
                ],
                'rotate_right' => [

                    'label' => 'ምስሉን ወደ ቀኝ አሽከርክር',
                ],
                'set_aspect_ratio' => [

                    'label' => 'ምስሉን ወደ :ratio መጥን',
                ],
                'save' => [

                    'label' => 'አስቀምጥ',
                ],
                'zoom_100' => [

                    'label' => 'ምስል ወደ 100% አሳድግ',
                ],
                'zoom_in' => [

                    'label' => 'አሳንስ',
                ],
                'zoom_out' => [

                    'label' => 'አሳድግ',
                ],
            ],
            'fields' => [

                'height' => [

                    'label' => 'ቁመት',
                    'unit' => 'px',
                ],
                'rotation' => [

                    'label' => 'አዙር',
                    'unit' => 'ድግሪ',
                ],
                'width' => [

                    'label' => 'ስፋት',
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

                'label' => 'ምጥጥነ ገጽታ',
                'no_fixed' => [

                    'label' => 'ነጻ',
                ],
            ],
            'svg' => [

                'messages' => [

                    'confirmation' => 'SVG ፋይሎችን ማስተካከል አይመከርም ምክንያቱም የጥራት መጥፋት ሊያስከትል ስለሚችል ነው።\\n እርግጠኛ ነዎት መቀጠል ይፈልጋሉ?',
                    'disabled' => 'የ SVG ፋይሎችን ማስተካከል አክቲቭ አይደለም። ምክንያቱም የጥራት መጥፋትን ሊያስከትል ስለሚችል ነው።',
                ],
            ],
        ],
    ],
    'key_value' => [

        'actions' => [

            'add' => [

                'label' => 'ረድፍ ለመጨመር',
            ],
            'delete' => [

                'label' => 'ረድፍ አጥፋ',
            ],
            'reorder' => [

                'label' => 'ረድፍን እንደገና ደርድር',
            ],
        ],
        'fields' => [

            'key' => [

                'label' => 'ስም',
            ],
            'value' => [

                'label' => 'ዋጋ',
            ],
        ],
    ],
    'markdown_editor' => [

        'tools' => [

            'attach_files' => 'ፋይል ለማጣመር',
            'blockquote' => 'ጥቅስ',
            'bold' => 'ለማጉላት',
            'bullet_list' => 'በነጥብ ዝርዝር',
            'code_block' => 'Code block',
            'heading' => 'ርዕስ',
            'italic' => 'Italic',
            'link' => 'ማስፈንጠርያ',
            'ordered_list' => 'በቁጥር ዝርዝር',
            'redo' => 'እንደገና',
            'strike' => 'በላዩላይ ለማስመር',
            'table' => 'ሰንጠረዝ',
            'undo' => 'ይቅር(ወደኋላ)',
        ],
    ],
    'radio' => [

        'boolean' => [

            'true' => 'አዎ',
            'false' => 'ኣይ',
        ],
    ],
    'repeater' => [

        'actions' => [

            'add' => [

                'label' => 'ወደ :label ጨምር',
            ],
            'add_between' => [

                'label' => 'መካከል አስገባ',
            ],
            'delete' => [

                'label' => 'አጥፋ',
            ],
            'clone' => [

                'label' => 'ቅዳ',
            ],
            'reorder' => [

                'label' => 'አንቀሳቅስ',
            ],
            'move_down' => [

                'label' => 'ወደ ታች አንቀሳቅስ',
            ],
            'move_up' => [

                'label' => 'ወደ ላይ አንቀሳቅስ',
            ],
            'collapse' => [

                'label' => 'ሰብስብ',
            ],
            'expand' => [

                'label' => 'ዘርጋ',
            ],
            'collapse_all' => [

                'label' => 'ሁሉንም ሰብስብ',
            ],
            'expand_all' => [

                'label' => 'ሁሉንም ዘርጋ',
            ],
        ],
    ],
    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [

                    'link' => 'አገናኝ',
                    'unlink' => 'አለያይ',
                ],
                'label' => 'URL',
                'placeholder' => 'URL አስገባ',
            ],
        ],
        'tools' => [

            'attach_files' => 'ፋይሎችን ያያይዙ',
            'blockquote' => 'Blockquote',
            'bold' => 'ለማጉላት',
            'bullet_list' => 'በነጥብ ዝርዝር',
            'code_block' => 'Code block',
            'h1' => 'ዋና ርዕስ',
            'h2' => 'ርዕስ',
            'h3' => 'ንኡስ ርዕስ',
            'italic' => 'Italic',
            'link' => 'ማስፈንጠርያ',
            'ordered_list' => 'በቁጥር ዝርዝር',
            'redo' => 'እንደገና',
            'strike' => 'በላዩላይ ለማስመር',
            'underline' => 'ለማስመር',
            'undo' => 'ይቅር(ወደኋላ)',
        ],
    ],
    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'መዝግብ',
                    'actions' => [

                        'create' => [

                            'label' => 'መዝግብ',
                        ],
                        'create_another' => [

                            'label' => 'መዝግብና ሌላ ምዝገባ ጀምር',
                        ],
                    ],
                ],
            ],
            'edit_option' => [

                'modal' => [

                    'heading' => 'አድስ',
                    'actions' => [

                        'save' => [

                            'label' => 'አስቀምጥ',
                        ],
                    ],
                ],
            ],
        ],
        'boolean' => [

            'true' => 'አዎ',
            'false' => 'ኣይ',
        ],
        'loading_message' => 'በመጫን ላይ...',
        'max_items_message' => 'መምረጥ የምቻለው :count ብቻ ነው።',
        'no_search_results_message' => 'ከፍለጋዎ ጋር የሚዛመዱ አማራጮች የሉም።',
        'placeholder' => 'ከአማራጮች ይምረጡ',
        'searching_message' => 'በመፈለግ ላይ...',
        'search_prompt' => 'ለመፈለግ መተየብ ይጀምሩ...',
    ],
    'tags_input' => [

        'placeholder' => 'አዲስ መለያ',
    ],
    'text_input' => [

        'actions' => [

            'hide_password' => [

                'label' => 'የይለፍ ቃል ደብቅ',
            ],
            'show_password' => [

                'label' => 'የይለፍ ቃል አሳይ',
            ],
        ],
    ],
    'toggle_buttons' => [

        'boolean' => [

            'true' => 'አዎ',
            'false' => 'ኣይ',
        ],
    ],
];

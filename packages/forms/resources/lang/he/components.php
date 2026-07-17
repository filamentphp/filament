<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'שכפל',
            ],

            'add' => [

                'label' => 'הוסף :label',

                'modal' => [

                    'heading' => 'הוספה ל:label',

                    'actions' => [

                        'add' => [
                            'label' => 'הוסף',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'הכנס בין הבלוקים',

                'modal' => [

                    'heading' => 'הוספה ל:label',

                    'actions' => [

                        'add' => [
                            'label' => 'הוספה',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'מחק',
            ],

            'edit' => [

                'label' => 'עריכה',

                'modal' => [

                    'heading' => 'עריכת בלוק',

                    'actions' => [

                        'save' => [
                            'label' => 'שמירת שינויים',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'העבר',
            ],

            'move_down' => [
                'label' => 'הזז מטה',
            ],

            'move_up' => [
                'label' => 'הזז מעלה',
            ],

            'collapse' => [
                'label' => 'צמצם',
            ],

            'expand' => [
                'label' => 'הרחב',
            ],

            'collapse_all' => [
                'label' => 'צמצם הכל',
            ],

            'expand_all' => [
                'label' => 'הרחב הכל',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'בטל בחירה בהכל',
            ],

            'select_all' => [
                'label' => 'בחר הכל',
            ],

        ],

    ],

    'color_picker' => [

        'panel_label' => 'בורר צבעים',

    ],

    'file_upload' => [

        'actions' => [

            'download' => [
                'label' => 'הורדה',
            ],

            'open' => [
                'label' => 'פתיחה בכרטיסיה חדשה',
            ],

        ],

        'editor' => [

            'label' => 'עורך תמונות',

            'actions' => [

                'cancel' => [
                    'label' => 'ביטול',
                ],

                'drag_crop' => [
                    'label' => 'מצב גרירה "חיתוך"',
                ],

                'drag_move' => [
                    'label' => 'מצב גרירה "הזזה"',
                ],

                'flip_horizontal' => [
                    'label' => 'היפוך אופקי של התמונה',
                ],

                'flip_vertical' => [
                    'label' => 'היפוך אנכי של התמונה',
                ],

                'move_down' => [
                    'label' => 'הזזת התמונה למטה',
                ],

                'move_left' => [
                    'label' => 'הזזת התמונה לשמאל',
                ],

                'move_right' => [
                    'label' => 'הזזת התמונה לימין',
                ],

                'move_up' => [
                    'label' => 'הזזת התמונה למעלה',
                ],

                'reset' => [
                    'label' => 'איפוס',
                ],

                'rotate_left' => [
                    'label' => 'סיבוב התמונה שמאלה',
                ],

                'rotate_right' => [
                    'label' => 'סיבוב התמונה ימינה',
                ],

                'set_aspect_ratio' => [
                    'label' => 'הגדר יחס צדדי לתמונה :ratio',
                ],

                'save' => [
                    'label' => 'שמירה',
                ],

                'zoom_100' => [
                    'label' => 'התמונה עם זום 100%',
                ],

                'zoom_in' => [
                    'label' => 'התקרב',
                ],

                'zoom_out' => [
                    'label' => 'התרחק',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'גובה',
                    'unit' => 'פיקסלים',
                ],

                'rotation' => [
                    'label' => 'סיבוב',
                    'unit' => 'מעלות',
                ],

                'width' => [
                    'label' => 'רוחב',
                    'unit' => 'פיקסלים',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'פיקסלים',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'פיקסלים',
                ],

            ],

            'aspect_ratios' => [

                'label' => 'יחסי ציר',

                'no_fixed' => [
                    'label' => 'חופשי',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'עריכת קובצי SVG אינה מומלצת מכיוון שהיא עלולה לפגוע באיכות בעת שינוי הגודל.\\n  האם להמשיך?',
                    'disabled' => 'עריכת קובצי SVG מושבתת מכיוון שהיא עלולה לפגוע באיכות בעת שינוי הגודל.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'הוסף שורה',
            ],

            'delete' => [
                'label' => 'מחק שורה',
            ],

            'reorder' => [
                'label' => 'סדר רשומה מחדש',
            ],

        ],

        'columns' => [

            'actions' => [
                'label' => 'פעולות',
            ],

            'reorder' => [
                'label' => 'שינוי סדר',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'שדה',
            ],

            'value' => [
                'label' => 'ערך',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'קבצים שהועלו חייבים להיות מסוג: :values.',

        'file_attachments_max_size_message' => 'קבצים שהועלו לא יכולים לחרוג מ-:max קילובייט.',

        'tools' => [
            'attach_files' => 'הוסף קבצים',
            'blockquote' => 'בלוק ציטוט',
            'bold' => 'מודגש',
            'bullet_list' => 'רשימת נקודות',
            'code_block' => 'בלוק קוד',
            'heading' => 'כותרת',
            'italic' => 'נטוי',
            'link' => 'קישור',
            'ordered_list' => 'רשימה ממוספרת',
            'redo' => 'שחזור',
            'strike' => 'כתיב מחדל',
            'table' => 'טבלה',
            'undo' => 'ביטול שחזור',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'בחירה',

                'actions' => [

                    'select' => [
                        'label' => 'בחירה',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'כן',
            'false' => 'לא',
        ],

    ],

    'repeater' => [

        'columns' => [

            'actions' => [
                'label' => 'פעולות',
            ],

            'reorder' => [
                'label' => 'שינוי סדר',
            ],

        ],

        'actions' => [

            'add' => [
                'label' => 'הוסף :label',
            ],

            'add_between' => [
                'label' => 'להכניס בין הפריטים',
            ],

            'delete' => [
                'label' => 'מחק',
            ],

            'clone' => [
                'label' => 'שכפל',
            ],

            'reorder' => [
                'label' => 'העבר',
            ],

            'move_down' => [
                'label' => 'הזז מטה',
            ],

            'move_up' => [
                'label' => 'הזז מעלה',
            ],

            'collapse' => [
                'label' => 'צמצם',
            ],

            'expand' => [
                'label' => 'הרחב',
            ],

            'collapse_all' => [
                'label' => 'צמצם הכל',
            ],

            'expand_all' => [
                'label' => 'הרחב הכל',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'העלאת קובץ',

                'modal' => [

                    'heading' => 'העלאת קובץ',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'קובץ',
                                'existing' => 'החלף קובץ',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'טקסט חלופי',
                                'existing' => 'שנה טקסט חלופי',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'הכנס',
                        ],

                        'save' => [
                            'label' => 'שמירה',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'רשת',

                'modal' => [

                    'heading' => 'רשת',

                    'form' => [

                        'preset' => [

                            'label' => 'תבנית מוכנה',

                            'placeholder' => 'ללא',

                            'options' => [
                                'two' => 'שתיים',
                                'three' => 'שלוש',
                                'four' => 'ארבע',
                                'five' => 'חמש',
                                'two_start_third' => 'שניים (להתחיל שלישי)',
                                'two_end_third' => 'שניים (לסיים שלישי)',
                                'two_start_fourth' => 'שניים (להתחיל רביעי)',
                                'two_end_fourth' => 'שניים (לסיים רביעי)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'עמודות',
                        ],

                        'from_breakpoint' => [

                            'label' => 'מנקודת שבירה',

                            'options' => [
                                'default' => 'הכל',
                                'sm' => 'קטן',
                                'md' => 'בינוני',
                                'lg' => 'גדול',
                                'xl' => 'גדול מאוד',
                                '2xl' => 'גדול במיוחד',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'שתי עמודות א-סימטריות',
                        ],

                        'start_span' => [
                            'label' => 'טווח התחלה',
                        ],

                        'end_span' => [
                            'label' => 'טווח סיום',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'עריכה',

                'modal' => [

                    'heading' => 'קישור',

                    'form' => [

                        'url' => [
                            'label' => 'כתובת URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'פתח קישור בכרטיסיה חדשה',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'צבע טקסט',

                'modal' => [

                    'heading' => 'צבע טקסט',

                    'form' => [

                        'color' => [
                            'label' => 'צבע',

                            'options' => [
                                'slate' => 'צפחה',
                                'gray' => 'אפור',
                                'zinc' => 'אבץ',
                                'neutral' => 'ניטרלי',
                                'stone' => 'אבן',
                                'mauve' => 'סגול בהיר',
                                'olive' => 'זית',
                                'mist' => 'ערפל',
                                'taupe' => 'חום אפרפר',
                                'red' => 'אדום',
                                'orange' => 'כתום',
                                'amber' => 'ענבר',
                                'yellow' => 'צהוב',
                                'lime' => 'ירוק ליים',
                                'green' => 'ירוק',
                                'emerald' => 'ירוק אזמרגד',
                                'teal' => 'טורקיז כהה',
                                'cyan' => 'ציאן',
                                'sky' => 'תכלת',
                                'blue' => 'כחול',
                                'indigo' => 'אינדיגו',
                                'violet' => 'סגול כחלחל',
                                'purple' => 'סגול',
                                'fuchsia' => 'פוקסיה',
                                'pink' => 'ורוד',
                                'rose' => 'ורוד עז',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'צבע מותאם אישית',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'קבצים שהועלו חייבים להיות מסוג: :values.',

        'file_attachments_max_size_message' => 'קבצים שהועלו לא יכולים לחרוג מ-:max קילובייט.',

        'no_merge_tag_search_results_message' => 'אין תוצאות לתגי מיזוג.',

        'mentions' => [
            'no_options_message' => 'אין אפשרויות זמינות.',
            'no_search_results_message' => 'לא נמצאו תוצאות.',
            'search_prompt' => 'התחל להקליד על מנת לחפש...',
            'searching_message' => 'מחפש...',
        ],

        'toolbar' => [
            'aria_label' => 'סרגל הכלים של העורך',
        ],

        'tools' => [
            'align_center' => 'יישר למרכז',
            'align_end' => 'יישר לשמאל',
            'align_justify' => 'יישר לשני הצדדים',
            'align_start' => 'יישר לימין',
            'attach_files' => 'הוסף קבצים',
            'blockquote' => 'בלוק ציטוט',
            'bold' => 'מודגש',
            'bullet_list' => 'רשימת נקודות',
            'clear_formatting' => 'ניקוי עיצוב',
            'code' => 'קוד',
            'code_block' => 'בלוק קוד',
            'custom_blocks' => 'בלוקים מותאמים אישית',
            'details' => 'פרטים',
            'h1' => 'כותרת 1',
            'h2' => 'כותרת 2',
            'h3' => 'כותרת 3',
            'h4' => 'כותרת 4',
            'h5' => 'כותרת 5',
            'h6' => 'כותרת 6',
            'grid' => 'רשת',
            'grid_delete' => 'מחיקת רשת',
            'highlight' => 'הדגשה',
            'horizontal_rule' => 'קו אופקי',
            'italic' => 'נטוי',
            'lead' => 'טקסט גדול',
            'link' => 'קישור',
            'merge_tags' => 'מזג תוויות',
            'ordered_list' => 'רשימה ממוספרת',
            'paragraph' => 'פסקה',
            'redo' => 'בצע שוב',
            'small' => 'טקסט קטן',
            'strike' => 'קו חוצה',
            'subscript' => 'כתב תחתי',
            'superscript' => 'כתב עילי',
            'table' => 'טבלה',
            'table_delete' => 'מחק טבלה',
            'table_add_column_before' => 'הוסף עמודה לפני',
            'table_add_column_after' => 'הוסף עמודה אחרי',
            'table_delete_column' => 'מחק עמודה',
            'table_add_row_before' => 'הוסף שורה לפני',
            'table_add_row_after' => 'הוסף שורה אחרי',
            'table_delete_row' => 'מחק שורה',
            'table_merge_cells' => 'מזג תאים',
            'table_split_cell' => 'פצל תאים',
            'table_toggle_header_row' => 'הפעל/הסתר שורת כותרת',
            'table_toggle_header_cell' => 'החלפת תא כותרת',
            'text_color' => 'צבע טקסט',
            'underline' => 'קו תחתון',
            'undo' => 'בטל',
        ],

        'uploading_file_message' => 'מעלה קובץ...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'יצירה',

                'modal' => [

                    'heading' => 'יצירה',

                    'actions' => [

                        'create' => [
                            'label' => 'יצירה',
                        ],

                        'create_another' => [
                            'label' => 'צור וצור עוד אחד',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'עריכה',

                'modal' => [

                    'heading' => 'עריכה',

                    'actions' => [

                        'save' => [
                            'label' => 'שמירה',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'כן',
            'false' => 'לא',
        ],

        'loading_message' => 'טוען...',

        'max_items_message' => 'ניתן לבחור רק :count',

        'no_options_message' => 'אין אפשרויות זמינות.',

        'no_search_results_message' => 'לא נמצאו תוצאות.',

        'placeholder' => 'בחר',

        'searching_message' => 'מחפש...',

        'search_prompt' => 'התחל להקליד על מנת לחפש...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'מחק',
            ],

        ],

        'placeholder' => 'תגית חדשה',

        'tag_added' => 'נוסף: :tag',

        'tag_removed' => 'הוסר: :tag',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'העתק',
                'message' => 'הועתק',
            ],

            'hide_password' => [
                'label' => 'הסתרת סיסמה',
            ],

            'show_password' => [
                'label' => 'הצגת סיסמה',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'כן',
            'false' => 'לא',
        ],

    ],

];

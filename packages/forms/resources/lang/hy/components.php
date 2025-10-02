<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Կրկնօրինակել',
            ],

            'add' => [

                'label' => 'Ավելացնել:label',

                'modal' => [

                    'heading' => 'Ավելացնել :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Ավելացնել',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Տեղադրել բլոկների միջև',

                'modal' => [

                    'heading' => 'Ավելացնել :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Ավելացնել',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Ջնջել',
            ],

            'edit' => [

                'label' => 'Խմբագրել',

                'modal' => [

                    'heading' => 'Խմբագրել բլոկը',

                    'actions' => [

                        'save' => [
                            'label' => 'Պահպանել փոփոխությունները',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Վերադասավորել',
            ],

            'move_down' => [
                'label' => 'Իջացնել',
            ],

            'move_up' => [
                'label' => 'Բարձրացնել',
            ],

            'collapse' => [
                'label' => 'Ծալել',
            ],

            'expand' => [
                'label' => 'Ընդարձակել',
            ],

            'collapse_all' => [
                'label' => 'Ծալել բոլորը',
            ],

            'expand_all' => [
                'label' => 'Ընդարձակել բոլորը',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Ապանշել բոլորը',
            ],

            'select_all' => [
                'label' => 'Ընտրել բոլորը',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Չեղարկել',
                ],

                'drag_crop' => [
                    'label' => 'Քաշեք և թողեք «կտրել» ռեժիմը',
                ],

                'drag_move' => [
                    'label' => 'Քաշեք և թողեք «տեղափոխել» ռեժիմը',
                ],

                'flip_horizontal' => [
                    'label' => 'Հորիզոնական շրջել',
                ],

                'flip_vertical' => [
                    'label' => 'Ուղղահայաց շրջել',
                ],

                'move_down' => [
                    'label' => 'Տեղափոխել ներքև',
                ],

                'move_left' => [
                    'label' => 'Տեղափոխել ձախ',
                ],

                'move_right' => [
                    'label' => 'Տեղափոխել աջ',
                ],

                'move_up' => [
                    'label' => 'Տեղափոխել վերև',
                ],

                'reset' => [
                    'label' => 'Վերականգնել',
                ],

                'rotate_left' => [
                    'label' => 'Պտտել ձախ',
                ],

                'rotate_right' => [
                    'label' => 'Պտտել աջ',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Սահմանել կողմերի հարաբերակցությունը այնպես, որ',
                ],

                'save' => [
                    'label' => 'Պահպանել',
                ],

                'zoom_100' => [
                    'label' => 'Պատկերը դարձնել 100%',
                ],

                'zoom_in' => [
                    'label' => 'Մեծացնել',
                ],

                'zoom_out' => [
                    'label' => 'Փոքրացնել',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Բարձր.',
                    'unit' => 'փքս',
                ],

                'rotation' => [
                    'label' => 'Պտույտ',
                    'unit' => 'աստ.',
                ],

                'width' => [
                    'label' => 'Լայն.',
                    'unit' => 'փքս',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'փքս',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'փքս',
                ],

            ],

            'aspect_ratios' => [

                'label' => 'Կողմերի հարաբերակցություններ',

                'no_fixed' => [
                    'label' => 'Ազատ',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG ֆայլերը խմբագրելը խորհուրդ չի տրվում, քանի որ դա կարող է հանգեցնել որակի կորստի, երբ այն մասշտաբավորվում է:',
                    'disabled' => 'SVG ֆայլերի խմբագրումն անջատված է, քանի որ դա կարող է հանգեցնել որակի կորստի, երբ մասշտաբավորվում է:',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Ավելացնել տող',
            ],

            'delete' => [
                'label' => 'Ջնջել տողը',
            ],

            'reorder' => [
                'label' => 'Վերադասավորել տողերը',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Բանալի',
            ],

            'value' => [
                'label' => 'Արժեք',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Վերբեռնված ֆայլերը պետք է լինեն հետևյալ տեսակներից՝ :values։',

        'file_attachments_max_size_message' => 'Վերբեռնված ֆայլերը չպետք է գերազանցեն :max կիլոբայթ։',

        'tools' => [
            'attach_files' => 'Կցել ֆայլեր',
            'blockquote' => 'Մեջբերում',
            'bold' => 'Թավ',
            'bullet_list' => 'Կետային ցուցակ',
            'code_block' => 'Կոդի բլոկ',
            'heading' => 'Վերնագիր',
            'italic' => 'Շեղ',
            'link' => 'Հղում',
            'ordered_list' => 'Համարակալված ցուցակ',
            'redo' => 'Կրկնել',
            'strike' => 'Գծանցում',
            'table' => 'Աղյուսակ',
            'undo' => 'Հետ բերել',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Ընտրել',

                'actions' => [

                    'select' => [
                        'label' => 'Ընտրել',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Այո',
            'false' => 'Ոչ',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Ավելացնել:label',
            ],

            'add_between' => [
                'label' => 'Տեղադրել միջև',
            ],

            'delete' => [
                'label' => 'Ջնջել',
            ],

            'clone' => [
                'label' => 'Կրկնօրինակել',
            ],

            'reorder' => [
                'label' => 'Վերադասավորել',
            ],

            'move_down' => [
                'label' => 'Իջացնել',
            ],

            'move_up' => [
                'label' => 'Բարձրացնել',
            ],

            'collapse' => [
                'label' => 'Ծալել',
            ],

            'expand' => [
                'label' => 'Ընդլայնել',
            ],

            'collapse_all' => [
                'label' => 'Ծալել բոլորը',
            ],

            'expand_all' => [
                'label' => 'Ընդլայնել բոլորը',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Վերբեռնել ֆայլ',

                'modal' => [

                    'heading' => 'Վերբեռնել ֆայլ',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Ֆայլ',
                                'existing' => 'Փոխարինել ֆայլը',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Այլընտրանքային տեքստ',
                                'existing' => 'Փոխել այլընտրանքային տեքստը',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Տեղադրել',
                        ],

                        'save' => [
                            'label' => 'Պահպանել',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Ցանց',

                'modal' => [

                    'heading' => 'Ցանց',

                    'form' => [

                        'preset' => [

                            'label' => 'Կանխադրված',
                            'placeholder' => 'Չկա',

                            'options' => [
                                'two' => 'Երկու',
                                'three' => 'Երեք',
                                'four' => 'Չորս',
                                'five' => 'Հինգ',
                                'two_start_third' => 'Երկու (սկսել երրորդից)',
                                'two_end_third' => 'Երկու (ավարտ երրորդում)',
                                'two_start_fourth' => 'Երկու (սկսել չորրորդից)',
                                'two_end_fourth' => 'Երկու (ավարտ չորրորդում)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Սյուներ',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Սկսած չափից',

                            'options' => [
                                'default' => 'Բոլորը',
                                'sm' => 'Փոքր',
                                'md' => 'Միջին',
                                'lg' => 'Մեծ',
                                'xl' => 'Շատ մեծ',
                                '2xl' => 'Երկու անգամ մեծ',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Երկու ասիմետրիկ սյուն',
                        ],

                        'start_span' => [
                            'label' => 'Սկսման լայնություն',
                        ],

                        'end_span' => [
                            'label' => 'Ավարտի լայնություն',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Հղում',

                'modal' => [

                    'heading' => 'Հղում',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Բացել նոր թաբում',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Տեքստի գույն',

                'modal' => [

                    'heading' => 'Տեքստի գույն',

                    'form' => [

                        'color' => [
                            'label' => 'Գույն',
                        ],

                        'custom_color' => [
                            'label' => 'Այլ գույն',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Վերբեռնված ֆայլերը պետք է լինեն հետևյալ տեսակներից՝ :values։',

        'file_attachments_max_size_message' => 'Վերբեռնված ֆայլերը չպետք է գերազանցեն :max կիլոբայթ։',

        'no_merge_tag_search_results_message' => 'Միաձուլման պիտակների արդյունքներ չկան։',

        'tools' => [
            'align_center' => 'Հավասարեցնել կենտրոնում',
            'align_end' => 'Հավասարեցնել վերջում',
            'align_justify' => 'Հավասարեցնել ըստ լայնության',
            'align_start' => 'Հավասարեցնել սկզբում',
            'attach_files' => 'Կցել ֆայլեր',
            'blockquote' => 'Մեջբերում',
            'bold' => 'Թավ',
            'bullet_list' => 'Կետային ցուցակ',
            'clear_formatting' => 'Մաքրել ձևաչափումը',
            'code' => 'Կոդ',
            'code_block' => 'Կոդի բլոկ',
            'custom_blocks' => 'Բլոկներ',
            'details' => 'Մանրամասներ',
            'h1' => 'Վերնագիր',
            'h2' => 'Ենթավերնագիր',
            'h3' => 'Ենթավերնագիր 2',
            'grid' => 'Ցանց',
            'grid_delete' => 'Ջնջել ցանցը',
            'highlight' => 'Նշել գույնով',
            'horizontal_rule' => 'Հորիզոնական գիծ',
            'italic' => 'Շեղ',
            'lead' => 'Առաջատար տեքստ',
            'link' => 'Հղում',
            'merge_tags' => 'Միաձուլման պիտակներ',
            'ordered_list' => 'Համարակալված ցուցակ',
            'redo' => 'Կրկնել',
            'small' => 'Փոքր տեքստ',
            'strike' => 'Գծանցում',
            'subscript' => 'Ենթագրություն',
            'superscript' => 'Վերգրություն',
            'table' => 'Աղյուսակ',
            'table_delete' => 'Ջնջել աղյուսակը',
            'table_add_column_before' => 'Ավելացնել սյուն առաջ',
            'table_add_column_after' => 'Ավելացնել սյուն հետո',
            'table_delete_column' => 'Ջնջել սյունը',
            'table_add_row_before' => 'Ավելացնել տող վերև',
            'table_add_row_after' => 'Ավելացնել տող ներքև',
            'table_delete_row' => 'Ջնջել տողը',
            'table_merge_cells' => 'Միացնել բջիջները',
            'table_split_cell' => 'Բաժանել բջիջը',
            'table_toggle_header_row' => 'Վերնագրային տող',
            'text_color' => 'Տեքստի գույն',
            'underline' => 'Ընդգծել',
            'undo' => 'Հետ բերել',
        ],

        'uploading_file_message' => 'Ֆայլի վերբեռնում...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Ստեղծել',

                'modal' => [

                    'heading' => 'Ստեղծել',

                    'actions' => [

                        'create' => [
                            'label' => 'Ստեղծել',
                        ],

                        'create_another' => [
                            'label' => 'Ստեղծել և ստեղծել մեկ այլ',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Խմբագրել',

                'modal' => [

                    'heading' => 'Խմբագրել',

                    'actions' => [

                        'save' => [
                            'label' => 'Պահպանել',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Այո',
            'false' => 'Ոչ',
        ],

        'loading_message' => 'Բեռնում...',

        'max_items_message' => 'Կարող եք :count ընտրել միայն մեկը:',

        'no_search_results_message' => 'Ոչ մի տվյալ չի համապատասխանում ձեր հարցմանը:',

        'placeholder' => 'Ընտրել տարբերակ',

        'searching_message' => 'Որոնում...',

        'search_prompt' => 'Սկսեք մուտքագրել որոնման համար...',

    ],

    'tags_input' => [
        'placeholder' => 'Նոր պիտակ',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Պատճենել',
                'message' => 'Պատճենվեց',
            ],

            'hide_password' => [
                'label' => 'Թաքցնել գաղտնաբառը',
            ],

            'show_password' => [
                'label' => 'Ցուցադրել գաղտնաբառը',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Այո',
            'false' => 'Ոչ',
        ],

    ],

];

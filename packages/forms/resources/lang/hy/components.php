<?php

return [

    'builder' => [

        'reordering' => [
            'item' => 'Բլոկ',
            'position' => ':label, դիրք :position՝ :count-ից',
            'moved' => ':label տեղափոխվեց :position դիրք՝ :count-ից',
        ],

        'actions' => [

            'clone' => [
                'label' => 'Կրկնօրինակել',
            ],

            'add' => [

                'label' => 'Ավելացնել :label',

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

        'block_picker' => [

            'no_search_results_message' => 'Ոչ մի բլոկ չի համապատասխանում ձեր որոնմանը։',

            'search_prompt' => 'Որոնել բլոկներ',

        ],

    ],

    'checkbox_list' => [

        'required_description' => 'Ընտրեք առնվազն մեկ տարբերակ։',

        'actions' => [

            'deselect_all' => [
                'label' => 'Ապանշել բոլորը',
            ],

            'select_all' => [
                'label' => 'Ընտրել բոլորը',
            ],

        ],

    ],

    'color_picker' => [

        'panel_label' => 'Գույնի ընտրիչ',

    ],

    'date_time_picker' => [

        'month_select' => [
            'label' => 'Ամիս',
        ],

        'year_input' => [
            'label' => 'Տարի',
        ],

        'hour_input' => [
            'label' => 'Ժամ',
        ],

        'minute_input' => [
            'label' => 'Րոպե',
        ],

        'second_input' => [
            'label' => 'Վայրկյան',
        ],

    ],

    'file_upload' => [

        'placeholder' => 'Վերբեռնված ֆայլեր չկան',

        'actions' => [

            'download' => [
                'label' => 'Ներբեռնել',
            ],

            'open' => [
                'label' => 'Բացել նոր ներդիրում',
            ],

        ],

        'editor' => [

            'label' => 'Պատկերի խմբագրիչ',

            'actions' => [

                'cancel' => [
                    'label' => 'Չեղարկել',
                ],

                'drag_crop' => [
                    'label' => 'Քաշելու ռեժիմ՝ «կտրել»',
                ],

                'drag_move' => [
                    'label' => 'Քաշելու ռեժիմ՝ «տեղափոխել»',
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
                    'label' => 'Սահմանել կողմերի հարաբերակցությունը՝ :ratio',
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
                    'unit' => 'պքս',
                ],

                'rotation' => [
                    'label' => 'Պտույտ',
                    'unit' => 'աստ.',
                ],

                'width' => [
                    'label' => 'Լայն.',
                    'unit' => 'պքս',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'պքս',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'պքս',
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
                    'confirmation' => 'SVG ֆայլերի խմբագրումը խորհուրդ չի տրվում, քանի որ մասշտաբավորելիս դա կարող է հանգեցնել որակի կորստի։\n Վստա՞հ եք, որ ցանկանում եք շարունակել։',
                    'disabled' => 'SVG ֆայլերի խմբագրումն անջատված է, քանի որ մասշտաբավորելիս դա կարող է հանգեցնել որակի կորստի։',
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

            'move_down' => [
                'label' => 'Իջացնել',
            ],

            'move_up' => [
                'label' => 'Բարձրացնել',
            ],

            'reorder' => [
                'label' => 'Վերադասավորել տողը',
            ],

        ],

        'columns' => [

            'actions' => [
                'label' => 'Գործողություններ',
            ],

            'reorder' => [
                'label' => 'Վերադասավորել',
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

        'reordering' => [
            'row' => 'Տող',
            'position' => ':label, դիրք :position՝ :count-ից',
            'moved' => ':label տեղափոխվեց :position դիրք՝ :count-ից',
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
            'undo' => 'Հետարկել',
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

        'reordering' => [
            'item' => 'Տարր',
            'position' => ':label, դիրք :position՝ :count-ից',
            'moved' => ':label տեղափոխվեց :position դիրք՝ :count-ից',
        ],

        'columns' => [

            'actions' => [
                'label' => 'Գործողություններ',
            ],

            'reorder' => [
                'label' => 'Վերադասավորել',
            ],

        ],

        'actions' => [

            'add' => [
                'label' => 'Ավելացնել :label',
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

            'close_panel' => [
                'label' => 'Փակել վահանակը',
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

                            'label' => 'Նախադրում',

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
                            'label' => 'Սյունակներ',
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
                            'label' => 'Երկու ասիմետրիկ սյունակ',
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
                            'label' => 'Բացել նոր ներդիրում',
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

                            'options' => [
                                'slate' => 'Թերթաքարագույն',
                                'gray' => 'Մոխրագույն',
                                'zinc' => 'Ցինկագույն',
                                'neutral' => 'Չեզոք',
                                'stone' => 'Քարագույն',
                                'mauve' => 'Բաց մանուշակագույն',
                                'olive' => 'Ձիթապտղագույն',
                                'mist' => 'Մառախլագույն',
                                'taupe' => 'Գորշ',
                                'red' => 'Կարմիր',
                                'orange' => 'Նարնջագույն',
                                'amber' => 'Սաթագույն',
                                'yellow' => 'Դեղին',
                                'lime' => 'Լայմագույն',
                                'green' => 'Կանաչ',
                                'emerald' => 'Զմրուխտագույն',
                                'teal' => 'Կապտականաչ',
                                'cyan' => 'Ցիան',
                                'sky' => 'Երկնագույն',
                                'blue' => 'Կապույտ',
                                'indigo' => 'Լեղակագույն',
                                'violet' => 'Մանուշակագույն',
                                'purple' => 'Ծիրանագույն',
                                'fuchsia' => 'Ֆուքսիա',
                                'pink' => 'Վարդագույն',
                                'rose' => 'Վարդակարմիր',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'Այլ գույն',
                        ],

                    ],

                ],

            ],

        ],

        'custom_blocks' => [

            'actions' => [

                'delete' => [
                    'label' => 'Ջնջել բլոկը',
                ],

                'edit' => [
                    'label' => 'Խմբագրել բլոկը',
                ],

            ],

            'no_search_results_message' => 'Ոչ մի բլոկ չի համապատասխանում ձեր որոնմանը։',

            'search_label' => 'Որոնել բլոկներ',

            'search_prompt' => 'Որոնել բլոկներ',

        ],

        'file_attachments_accepted_file_types_message' => 'Վերբեռնված ֆայլերը պետք է լինեն հետևյալ տեսակներից՝ :values։',

        'file_attachments_max_size_message' => 'Վերբեռնված ֆայլերը չպետք է գերազանցեն :max կիլոբայթ։',

        'no_merge_tag_search_results_message' => 'Միաձուլման պիտակների արդյունքներ չկան',

        'mentions' => [
            'no_options_message' => 'Տարբերակներ չկան',
            'no_search_results_message' => 'Ոչ մի արդյունք չի համապատասխանում ձեր որոնմանը։',
            'search_prompt' => 'Սկսեք մուտքագրել որոնման համար',
            'searching_message' => 'Որոնում...',
        ],

        'toolbar' => [
            'label' => 'Խմբագրիչի գործիքագոտի',
        ],

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
            'h2' => 'Վերնագիր 2',
            'h3' => 'Վերնագիր 3',
            'h4' => 'Վերնագիր 4',
            'h5' => 'Վերնագիր 5',
            'h6' => 'Վերնագիր 6',
            'grid' => 'Ցանց',
            'grid_delete' => 'Ջնջել ցանցը',
            'highlight' => 'Նշել գույնով',
            'horizontal_rule' => 'Հորիզոնական գիծ',
            'italic' => 'Շեղ',
            'lead' => 'Ներածական տեքստ',
            'link' => 'Հղում',
            'merge_tags' => 'Միաձուլման պիտակներ',
            'ordered_list' => 'Համարակալված ցուցակ',
            'paragraph' => 'Պարբերություն',
            'redo' => 'Կրկնել',
            'small' => 'Փոքր տեքստ',
            'strike' => 'Գծանցում',
            'subscript' => 'Ստորին ինդեքս',
            'superscript' => 'Վերին ինդեքս',
            'table' => 'Աղյուսակ',
            'table_delete' => 'Ջնջել աղյուսակը',
            'table_add_column_before' => 'Ավելացնել սյունակ առաջ',
            'table_add_column_after' => 'Ավելացնել սյունակ հետո',
            'table_delete_column' => 'Ջնջել սյունակը',
            'table_add_row_before' => 'Ավելացնել տող վերև',
            'table_add_row_after' => 'Ավելացնել տող ներքև',
            'table_delete_row' => 'Ջնջել տողը',
            'table_merge_cells' => 'Միացնել բջիջները',
            'table_split_cell' => 'Բաժանել բջիջը',
            'table_toggle_header_row' => 'Վերնագրային տող',
            'table_toggle_header_cell' => 'Վերնագրային բջիջ',
            'text_color' => 'Տեքստի գույն',
            'underline' => 'Ընդգծել',
            'undo' => 'Հետարկել',
        ],

        'uploading_file_message' => 'Ֆայլի վերբեռնում...',

    ],

    'select' => [

        'actions' => [

            'clear' => [
                'label' => 'Մաքրել ընտրությունը',
            ],

            'create_option' => [

                'label' => 'Ստեղծել',

                'modal' => [

                    'heading' => 'Ստեղծել',

                    'actions' => [

                        'create' => [
                            'label' => 'Ստեղծել',
                        ],

                        'create_another' => [
                            'label' => 'Ստեղծել և ստեղծել ևս մեկը',
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

            'remove_option' => [
                'label' => 'Հեռացնել :label',
            ],

        ],

        'boolean' => [
            'true' => 'Այո',
            'false' => 'Ոչ',
        ],

        'error_message' => 'Տարբերակները հնարավոր չեղավ բեռնել։ Խնդրում ենք կրկին փորձել։',

        'loading_message' => 'Բեռնում...',

        'max_items_message' => 'Կարելի է ընտրել միայն :count։',

        'no_options_message' => 'Տարբերակներ չկան',

        'no_search_results_message' => 'Ոչ մի տարբերակ չի համապատասխանում ձեր որոնմանը։',

        'placeholder' => 'Ընտրեք տարբերակ',

        'required_description' => 'Ընտրեք տարբերակ։',

        'searching_message' => 'Որոնում...',

        'search_label' => 'Որոնում',

        'search_prompt' => 'Սկսեք մուտքագրել որոնման համար',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Ջնջել',
            ],

        ],

        'placeholder' => 'Նոր պիտակ',

        'tag_added' => 'Ավելացվեց՝ :tag',

        'tag_removed' => 'Հեռացվեց՝ :tag',

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

        'required_description' => 'Ընտրեք առնվազն մեկ տարբերակ։',

        'boolean' => [
            'true' => 'Այո',
            'false' => 'Ոչ',
        ],

    ],

];

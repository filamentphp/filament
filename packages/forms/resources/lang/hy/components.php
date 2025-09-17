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

        'tools' => [
            'attach_files' => 'Կցել ֆայլեր',
            'blockquote' => 'Մեջբերում',
            'bold' => 'Թավ',
            'bullet_list' => 'Կետային ցուցակ',
            'code_block' => 'Կոդային բլոկ',
            'heading' => 'Վերնագիր',
            'italic' => 'Շեղատառ',
            'link' => 'Հղում',
            'ordered_list' => 'Համարակալված ցուցակ',
            'redo' => 'Վերափոխել',
            'strike' => 'Խաչված',
            'table' => 'Աղյուսակ',
            'undo' => 'Հետարկել',
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

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Հղում',
                    'unlink' => 'Անջատել',
                ],

                'label' => 'URL',

                'placeholder' => 'Մուտքագրեք URL-ը',

            ],

        ],

        'tools' => [
            'attach_files' => 'Կցել ֆայլեր',
            'blockquote' => 'Մեջբերում բլոկից',
            'bold' => 'Թավ',
            'bullet_list' => 'Կետավոր ցանկ',
            'code_block' => 'Կոդային բլոկ',
            'h1' => 'Անվանում',
            'h2' => 'Վերնագիր',
            'h3' => 'Ենթավերնագիր',
            'italic' => 'Շեղատառ',
            'link' => 'Հղում',
            'ordered_list' => 'Համարակալված ցուցակ',
            'redo' => 'Վերափոխել',
            'strike' => 'Խաչված',
            'underline' => 'Ընդգծել',
            'undo' => 'Չեղարկել',
        ],

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

            'hide_password' => [
                'label' => 'Թաքցնել գաղտնաբառը',
            ],

            'show_password' => [
                'label' => 'Ցույց տալ գաղտնաբառը',
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

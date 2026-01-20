<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Клонирај',
            ],

            'add' => [

                'label' => 'Додади на :label',

                'modal' => [

                    'heading' => 'Додади на :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Додади',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Вметни помеѓу блокови',

                'modal' => [

                    'heading' => 'Додади на :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Додади',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Избриши',
            ],

            'edit' => [

                'label' => 'Уреди',

                'modal' => [

                    'heading' => 'Уреди блок',

                    'actions' => [

                        'save' => [
                            'label' => 'Зачувај промени',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Премести',
            ],

            'move_down' => [
                'label' => 'Премести надолу',
            ],

            'move_up' => [
                'label' => 'Премести нагоре',
            ],

            'collapse' => [
                'label' => 'Собери',
            ],

            'expand' => [
                'label' => 'Прошири',
            ],

            'collapse_all' => [
                'label' => 'Собери сите',
            ],

            'expand_all' => [
                'label' => 'Прошири сите',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Одбери сите',
            ],

            'select_all' => [
                'label' => 'Избери сите',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Откажи',
                ],

                'drag_crop' => [
                    'label' => 'Влечи режим "исечи"',
                ],

                'drag_move' => [
                    'label' => 'Влечи режим "премести"',
                ],

                'flip_horizontal' => [
                    'label' => 'Преврти слика хоризонтално',
                ],

                'flip_vertical' => [
                    'label' => 'Преврти слика вертикално',
                ],

                'move_down' => [
                    'label' => 'Премести слика надолу',
                ],

                'move_left' => [
                    'label' => 'Премести слика налево',
                ],

                'move_right' => [
                    'label' => 'Премести слика надесно',
                ],

                'move_up' => [
                    'label' => 'Премести слика нагоре',
                ],

                'reset' => [
                    'label' => 'Ресетирај',
                ],

                'rotate_left' => [
                    'label' => 'Ротирај слика налево',
                ],

                'rotate_right' => [
                    'label' => 'Ротирај слика надесно',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Постави сооднос на :ratio',
                ],

                'save' => [
                    'label' => 'Зачувај',
                ],

                'zoom_100' => [
                    'label' => 'Зумирај слика на 100%',
                ],

                'zoom_in' => [
                    'label' => 'Зумирај внатре',
                ],

                'zoom_out' => [
                    'label' => 'Зумирај надвор',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Висина',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Ротација',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Ширина',
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

                'label' => 'Соодноси',

                'no_fixed' => [
                    'label' => 'Слободно',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Уредувањето на SVG датотеки не се препорачува бидејќи може да резултира со загуба на квалитет при скалирање.\n Дали сте сигурни дека сакате да продолжите?',
                    'disabled' => 'Уредувањето на SVG датотеки е оневозможено бидејќи може да резултира со загуба на квалитет при скалирање.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Додади ред',
            ],

            'delete' => [
                'label' => 'Избриши ред',
            ],

            'reorder' => [
                'label' => 'Преуреди ред',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Клуч',
            ],

            'value' => [
                'label' => 'Вредност',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Прикачените датотеки мора да бидат од тип: :values.',

        'file_attachments_max_size_message' => 'Прикачените датотеки не смеат да бидат поголеми од :max килобајти.',

        'tools' => [
            'attach_files' => 'Прикачи датотеки',
            'blockquote' => 'Блок цитат',
            'bold' => 'Задебелено',
            'bullet_list' => 'Список со точки',
            'code_block' => 'Блок на код',
            'heading' => 'Наслов',
            'italic' => 'Курзив',
            'link' => 'Линк',
            'ordered_list' => 'Нумериран список',
            'redo' => 'Повтори',
            'strike' => 'Прецртано',
            'table' => 'Табела',
            'undo' => 'Откажи',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Избери',

                'actions' => [

                    'select' => [
                        'label' => 'Избери',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Да',
            'false' => 'Не',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Додади на :label',
            ],

            'add_between' => [
                'label' => 'Вметни помеѓу',
            ],

            'delete' => [
                'label' => 'Избриши',
            ],

            'clone' => [
                'label' => 'Клонирај',
            ],

            'reorder' => [
                'label' => 'Премести',
            ],

            'move_down' => [
                'label' => 'Премести надолу',
            ],

            'move_up' => [
                'label' => 'Премести нагоре',
            ],

            'collapse' => [
                'label' => 'Собери',
            ],

            'expand' => [
                'label' => 'Прошири',
            ],

            'collapse_all' => [
                'label' => 'Собери сите',
            ],

            'expand_all' => [
                'label' => 'Прошири сите',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Прикачи датотека',

                'modal' => [

                    'heading' => 'Прикачи датотека',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Датотека',
                                'existing' => 'Замени датотека',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Алт текст',
                                'existing' => 'Смени алт текст',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Вметни',
                        ],

                        'save' => [
                            'label' => 'Зачувај',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Мрежа',

                'modal' => [

                    'heading' => 'Мрежа',

                    'form' => [

                        'preset' => [

                            'label' => 'Предодредено',

                            'placeholder' => 'Нема',

                            'options' => [
                                'two' => 'Две',
                                'three' => 'Три',
                                'four' => 'Четири',
                                'five' => 'Пет',
                                'two_start_third' => 'Две (Започни Трето)',
                                'two_end_third' => 'Две (Заврши Трето)',
                                'two_start_fourth' => 'Две (Започни Четврто)',
                                'two_end_fourth' => 'Две (Заврши Четврто)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Колони',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Од точка на прекин',

                            'options' => [
                                'default' => 'Сите',
                                'sm' => 'Мал',
                                'md' => 'Среден',
                                'lg' => 'Голем',
                                'xl' => 'Многу голем',
                                '2xl' => 'Две многу големи',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Две асиметрични колони',
                        ],

                        'start_span' => [
                            'label' => 'Започни опсег',
                        ],

                        'end_span' => [
                            'label' => 'Заврши опсег',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Линк',

                'modal' => [

                    'heading' => 'Линк',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Отвори во нов таб',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Боја на текст',

                'modal' => [

                    'heading' => 'Боја на текст',

                    'form' => [

                        'color' => [
                            'label' => 'Боја',
                        ],

                        'custom_color' => [
                            'label' => 'Прилагодена боја',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Прикачените датотеки мора да бидат од тип: :values.',

        'file_attachments_max_size_message' => 'Прикачените датотеки не смеат да бидат поголеми од :max килобајти.',

        'no_merge_tag_search_results_message' => 'Нема резултати за спојување на тагови.',

        'mentions' => [
            'no_options_message' => 'Нема достапни опции.',
            'no_search_results_message' => 'Нема резултати кои се совпаѓаат со вашето пребарување.',
            'search_prompt' => 'Започнете да пишувате за пребарување...',
            'searching_message' => 'Се пребарува...',
        ],

        'tools' => [
            'align_center' => 'Порамни по центар',
            'align_end' => 'Порамни на крај',
            'align_justify' => 'Порамни обострано',
            'align_start' => 'Порамни на почеток',
            'attach_files' => 'Прикачи датотеки',
            'blockquote' => 'Блок цитат',
            'bold' => 'Задебелено',
            'bullet_list' => 'Список со точки',
            'clear_formatting' => 'Исчисти форматирање',
            'code' => 'Код',
            'code_block' => 'Блок на код',
            'custom_blocks' => 'Блокови',
            'details' => 'Детали',
            'h1' => 'Наслов',
            'h2' => 'Заглавие',
            'h3' => 'Подзаглавие',
            'grid' => 'Мрежа',
            'grid_delete' => 'Избриши мрежа',
            'highlight' => 'Истакни',
            'horizontal_rule' => 'Хоризонтална линија',
            'italic' => 'Курзив',
            'lead' => 'Водечки текст',
            'link' => 'Линк',
            'merge_tags' => 'Спој тагови',
            'ordered_list' => 'Нумериран список',
            'redo' => 'Повтори',
            'small' => 'Мал текст',
            'strike' => 'Прецртано',
            'subscript' => 'Подзнак',
            'superscript' => 'Надзнак',
            'table' => 'Табела',
            'table_delete' => 'Избриши табела',
            'table_add_column_before' => 'Додади колона пред',
            'table_add_column_after' => 'Додади колона после',
            'table_delete_column' => 'Избриши колона',
            'table_add_row_before' => 'Додади ред погоре',
            'table_add_row_after' => 'Додади ред подоле',
            'table_delete_row' => 'Избриши ред',
            'table_merge_cells' => 'Спој ќелии',
            'table_split_cell' => 'Подели ќелија',
            'table_toggle_header_row' => 'Превклучи заглавен ред',
            'table_toggle_header_cell' => 'Превклучи заглавна ќелија',
            'text_color' => 'Боја на текст',
            'underline' => 'Подвлечено',
            'undo' => 'Откажи',
        ],

        'uploading_file_message' => 'Се прикачува датотека...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Креирај',

                'modal' => [

                    'heading' => 'Креирај',

                    'actions' => [

                        'create' => [
                            'label' => 'Креирај',
                        ],

                        'create_another' => [
                            'label' => 'Креирај и креирај друг',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Уреди',

                'modal' => [

                    'heading' => 'Уреди',

                    'actions' => [

                        'save' => [
                            'label' => 'Зачувај',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Да',
            'false' => 'Не',
        ],

        'loading_message' => 'Се вчитува...',

        'max_items_message' => 'Само :count можат да бидат избрани.',

        'no_options_message' => 'Нема достапни опции.',

        'no_search_results_message' => 'Нема опции кои се совпаѓаат со вашето пребарување.',

        'placeholder' => 'Избери опција',

        'searching_message' => 'Се пребарува...',

        'search_prompt' => 'Започнете да пишувате за пребарување...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Избриши',
            ],

        ],

        'placeholder' => 'Нов таг',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Копирај',
                'message' => 'Копирано',
            ],

            'hide_password' => [
                'label' => 'Сокриј лозинка',
            ],

            'show_password' => [
                'label' => 'Прикажи лозинка',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Да',
            'false' => 'Не',
        ],

    ],

];

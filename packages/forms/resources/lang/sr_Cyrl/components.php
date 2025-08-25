<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Клонирај',
            ],

            'add' => [
                'label' => 'Додај на :label',

                'modal' => [

                    'heading' => 'Додај на :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Додај',
                        ],

                    ],

                ],
            ],

            'add_between' => [
                'label' => 'Уметни између блокова',

                'modal' => [

                    'heading' => 'Додај на :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Додај',
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
                            'label' => 'Сачувај измене',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Помакни',
            ],

            'move_down' => [
                'label' => 'Помакни доле',
            ],

            'move_up' => [
                'label' => 'Помакни горе',
            ],

            'collapse' => [
                'label' => 'Скупи',
            ],

            'expand' => [
                'label' => 'Прошири',
            ],

            'collapse_all' => [
                'label' => 'Скупи све',
            ],

            'expand_all' => [
                'label' => 'Прошири све',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Означи све',
            ],

            'select_all' => [
                'label' => 'Означи све',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Одустани',
                ],

                'drag_crop' => [
                    'label' => 'Повлачење за обрезивање',
                ],

                'drag_move' => [
                    'label' => 'Повлачење за премештање',
                ],

                'flip_horizontal' => [
                    'label' => 'Обрни слику хоризонтално',
                ],

                'flip_vertical' => [
                    'label' => 'Обрни слику вертикално',
                ],

                'move_down' => [
                    'label' => 'Помакни слику доле',
                ],

                'move_left' => [
                    'label' => 'Помакни слику лево',
                ],

                'move_right' => [
                    'label' => 'Помакни слику десно',
                ],

                'move_up' => [
                    'label' => 'Помакни слику горе',
                ],

                'reset' => [
                    'label' => 'Поништи',
                ],

                'rotate_left' => [
                    'label' => 'Окрени слику улево',
                ],

                'rotate_right' => [
                    'label' => 'Окрени слику удесно',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Постави однос ширине и висине на :ratio',
                ],

                'save' => [
                    'label' => 'Сачувај',
                ],

                'zoom_100' => [
                    'label' => 'Увећај слику на 100%',
                ],

                'zoom_in' => [
                    'label' => 'Повећај',
                ],

                'zoom_out' => [
                    'label' => 'Смањи',
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

                'label' => 'Однос ширине и висине',

                'no_fixed' => [
                    'label' => 'Слободно',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Обрађивање SVG датотека није препоручљиво и може довести до губитка квалитета када се скалира.\nЈесте ли сигурни да желите наставити?',
                    'disabled' => 'Обрађивање SVG датотека је онемогућено јер може довести до губитка квалитета када се скалира.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Додај ред',
            ],

            'delete' => [
                'label' => 'Избриши ред',
            ],

            'reorder' => [
                'label' => 'Промени редослед реда',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Кључ',
            ],

            'value' => [
                'label' => 'Вредност',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Додај датотеке',
            'blockquote' => 'Цитат',
            'bold' => 'Подебљано',
            'bullet_list' => 'Листа',
            'code_block' => 'Блок кода',
            'heading' => 'Заглавље',
            'italic' => 'Накошено',
            'link' => 'Веза',
            'ordered_list' => 'Нумерисана листа',
            'redo' => 'Понови',
            'strike' => 'Прецртано',
            'table' => 'Таблица',
            'undo' => 'Поништи',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Изборник',

                'actions' => [

                    'select' => [
                        'label' => 'Изборник',
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
                'label' => 'Додај на :label',
            ],

            'add_between' => [
                'label' => 'Уметни између',
            ],

            'delete' => [
                'label' => 'Избриши',
            ],

            'clone' => [
                'label' => 'Клонирај',
            ],

            'reorder' => [
                'label' => 'Помакни',
            ],

            'move_down' => [
                'label' => 'Помакни доле',
            ],

            'move_up' => [
                'label' => 'Помакни горе',
            ],

            'collapse' => [
                'label' => 'Скупи',
            ],

            'expand' => [
                'label' => 'Прошири',
            ],

            'collapse_all' => [
                'label' => 'Скупи све',
            ],

            'expand_all' => [
                'label' => 'Прошири све',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Пошаљи датотеку',

                'modal' => [

                    'heading' => 'Слање датотеке',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Датотека',
                                'existing' => 'Замени датотеку',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Алтернативни текст',
                                'existing' => 'Измена алтернативног текста',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Уметни',
                        ],

                        'save' => [
                            'label' => 'Сачувај',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Измени',

                'modal' => [

                    'heading' => 'Веза',

                    'form' => [

                        'url' => [
                            'label' => 'Интернет адреса',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Отвори у новом табу',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Без резултата спојених ознака.',

        'tools' => [
            'align_center' => 'Центрирано',
            'align_end' => 'Десно поравнање',
            'align_justify' => 'Обострано поравнање',
            'align_start' => 'Лево поравнање',
            'attach_files' => 'Додај датотеке',
            'blockquote' => 'Цитат',
            'bold' => 'Подебљано',
            'bullet_list' => 'Листа',
            'clear_formatting' => 'Уклони форматирање',
            'code' => 'Код',
            'code_block' => 'Блок кода',
            'custom_blocks' => 'Прилагођен блок',
            'details' => 'Детаљи',
            'h1' => 'Наслов',
            'h2' => 'Заглавље',
            'h3' => 'Поднаслов',
            'highlight' => 'Означи',
            'horizontal_rule' => 'Хоризонтална линија',
            'italic' => 'Накошено',
            'lead' => 'Уводни текст',
            'link' => 'Веза',
            'merge_tags' => 'Спојене ознаке',
            'ordered_list' => 'Нумерисана листа',
            'redo' => 'Понови',
            'small' => 'Мали текст',
            'strike' => 'Прецртано',
            'subscript' => 'Индекс',
            'superscript' => 'Експонент',
            'table' => 'Табела',
            'table_delete' => 'Избриши табелу',
            'table_add_column_before' => 'Додај колону испред',
            'table_add_column_after' => 'Додај колону после',
            'table_delete_column' => 'Избриши колону',
            'table_add_row_before' => 'Додај ред изнад',
            'table_add_row_after' => 'Додај ред испод',
            'table_delete_row' => 'Избриши ред',
            'table_merge_cells' => 'Споји ћелије',
            'table_split_cell' => 'Раздели ћелију',
            'table_toggle_header_row' => 'Прикажи/скриј ред заглавља',
            'underline' => 'Подвучено',
            'undo' => 'Поништи',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Направи',

                'modal' => [

                    'heading' => 'Направи',

                    'actions' => [

                        'create' => [
                            'label' => 'Направи',
                        ],

                        'create_another' => [
                            'label' => 'Направи и додај још један',
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
                            'label' => 'Сачувај',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Да',
            'false' => 'Не',
        ],

        'loading_message' => 'Учитавање...',

        'max_items_message' => 'Само :count може бити изабрано.',

        'no_search_results_message' => 'Нема опција које одговарају претрази.',

        'placeholder' => 'Изабери опцију',

        'searching_message' => 'Претраживање...',

        'search_prompt' => 'Почни куцати за претрагу...',

    ],

    'tags_input' => [
        'placeholder' => 'Нова ознака',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Сакриј лозинку',
            ],

            'show_password' => [
                'label' => 'Прикажи лозинку',
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

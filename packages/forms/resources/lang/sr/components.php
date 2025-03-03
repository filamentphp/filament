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
                'label' => 'Обриши',
            ],

            'edit' => [

                'label' => 'Уреди',

                'modal' => [

                    'heading' => 'Уреди блок',

                    'actions' => [

                        'save' => [
                            'label' => 'Сними измене',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Помери',
            ],

            'move_down' => [
                'label' => 'Помери доле',
            ],

            'move_up' => [
                'label' => 'Помери горе',
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
                'label' => 'Деселектуј све',
            ],

            'select_all' => [
                'label' => 'Селектуј све',
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
                    'label' => 'Превлачење - исеци',
                ],

                'drag_move' => [
                    'label' => 'Превлачење - помери',
                ],

                'flip_horizontal' => [
                    'label' => 'Обрни слику водоравно',
                ],

                'flip_vertical' => [
                    'label' => 'Обрни слику усправно',
                ],

                'move_down' => [
                    'label' => 'Помери слику доле',
                ],

                'move_left' => [
                    'label' => 'Помери слику лево',
                ],

                'move_right' => [
                    'label' => 'Помери слику десно',
                ],

                'move_up' => [
                    'label' => 'Помери слику горе',
                ],

                'reset' => [
                    'label' => 'Поништи',
                ],

                'rotate_left' => [
                    'label' => 'Ротитај слику улево',
                ],

                'rotate_right' => [
                    'label' => 'Ротитај слику удесно',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Постави однос ширине и висине на :ratio',
                ],

                'save' => [
                    'label' => 'Сними',
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
                    'label' => 'Шитина',
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
                    'confirmation' => 'Обрађивање SVG датотека није препоручљиво и може довести до губитка квалитета када се скалира.\n Да ли си сигуран/на да желиш наставити?',
                    'disabled' => 'Онемогућена је обрада SVG датотека јер може довести до губитка квалитета када се скалира.',
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
                'label' => 'Обриши ред',
            ],

            'reorder' => [
                'label' => 'Промени редослед редова',
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

        'toolbar_buttons' => [
            'attach_files' => 'Додај фајлове',
            'blockquote' => 'Блок цитат',
            'bold' => 'Подебљано',
            'bullet_list' => 'Листа',
            'code_block' => 'Блок кôда',
            'heading' => 'Заглавље',
            'italic' => 'Курзив',
            'link' => 'Повезница',
            'ordered_list' => 'Нумерисана листа',
            'redo' => 'Понови',
            'strike' => 'Прецртано',
            'table' => 'Таблица',
            'undo' => 'Поништи',
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
                'label' => 'Обриши',
            ],

            'clone' => [
                'label' => 'Клонирај',
            ],

            'reorder' => [
                'label' => 'Помери',
            ],

            'move_down' => [
                'label' => 'Помери доле',
            ],

            'move_up' => [
                'label' => 'Помери горе',
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

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Линк',
                    'unlink' => 'уклони линк',
                ],

                'label' => 'URL',

                'placeholder' => 'Унеси URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Додај фајлове',
            'blockquote' => 'Блок цитат',
            'bold' => 'Подебљано',
            'bullet_list' => 'Листа',
            'code_block' => 'Блок кôда',
            'h1' => 'Наслив',
            'h2' => 'Заглавље',
            'h3' => 'Поднаслов',
            'italic' => 'Кутзив',
            'link' => 'Линк',
            'ordered_list' => 'Нумерисана листа',
            'redo' => 'Понови',
            'strike' => 'Прецртано',
            'underline' => 'Подцртано',
            'undo' => 'Поништи',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

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

                'modal' => [

                    'heading' => 'Уреди',

                    'actions' => [

                        'save' => [
                            'label' => 'Сними',
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

        'max_items_message' => 'Максимално :count можеш исабрати.',

        'no_search_results_message' => 'Нема резултата претраге.',

        'placeholder' => 'Одабери опцију',

        'searching_message' => 'Претрага...',

        'search_prompt' => 'Куцај за апретрагу...',

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

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Назад',
            ],

            'next_step' => [
                'label' => 'Напред',
            ],

        ],

    ],

];

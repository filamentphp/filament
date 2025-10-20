<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Клонирай',
            ],

            'add' => [
                'label' => 'Добави към :label',
            ],

            'add_between' => [
                'label' => 'Вмъкни между блоковете',
            ],

            'delete' => [
                'label' => 'Изтриване',
            ],

            'reorder' => [
                'label' => 'Преместване',
            ],

            'move_down' => [
                'label' => 'Преместване надолу',
            ],

            'move_up' => [
                'label' => 'Преместване нагоре',
            ],

            'collapse' => [
                'label' => 'Свиване',
            ],

            'expand' => [
                'label' => 'Разширяване',
            ],

            'collapse_all' => [
                'label' => 'Свиване на всички',
            ],

            'expand_all' => [
                'label' => 'Разширяване на всички',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Отмаркирай всички',
            ],

            'select_all' => [
                'label' => 'Избери всички',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Отказ',
                ],

                'drag_crop' => [
                    'label' => 'Влачене режим "изрязване"',
                ],

                'drag_move' => [
                    'label' => 'Влачене режим "преместване"',
                ],

                'flip_horizontal' => [
                    'label' => 'Обърни изображението хоризонтално',
                ],

                'flip_vertical' => [
                    'label' => 'Обърни изображението вертикално',
                ],

                'move_down' => [
                    'label' => 'Преместване надолу',
                ],

                'move_left' => [
                    'label' => 'Преместване наляво',
                ],

                'move_right' => [
                    'label' => 'Преместване надясно',
                ],

                'move_up' => [
                    'label' => 'Преместване нагоре',
                ],

                'reset' => [
                    'label' => 'Нулиране',
                ],

                'rotate_left' => [
                    'label' => 'Завъртане на изображението наляво',
                ],

                'rotate_right' => [
                    'label' => 'Завъртане на изображението надясно',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Задай съотношение на страните на :ratio',
                ],

                'save' => [
                    'label' => 'Запазване',
                ],

                'zoom_100' => [
                    'label' => 'Увеличение 100%',
                ],

                'zoom_in' => [
                    'label' => 'Увеличаване',
                ],

                'zoom_out' => [
                    'label' => 'Намаляне',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Височина',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Ротация',
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

                'label' => 'Съотношение на страните',

                'no_fixed' => [
                    'label' => 'Нефиксирано',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Добави ред',
            ],

            'delete' => [
                'label' => 'Изтрий ред',
            ],

            'reorder' => [
                'label' => 'Пренареди редове',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Ключ',
            ],

            'value' => [
                'label' => 'Стойност',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Прикачи файлове',
            'blockquote' => 'Цитат',
            'bold' => 'Удебелен текст',
            'bullet_list' => 'Списък с точки',
            'code_block' => 'Код',
            'heading' => 'Заглавие',
            'italic' => 'Курсив',
            'link' => 'Връзка',
            'ordered_list' => 'Номериран списък',
            'redo' => 'Повтори',
            'strike' => 'Зачертан текст',
            'table' => 'Таблица',
            'undo' => 'Отмени',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Добави към :label',
            ],

            'delete' => [
                'label' => 'Изтриване',
            ],

            'clone' => [
                'label' => 'Клониране',
            ],

            'reorder' => [
                'label' => 'Преместване',
            ],

            'move_down' => [
                'label' => 'Преместване надолу',
            ],

            'move_up' => [
                'label' => 'Преместване нагоре',
            ],

            'collapse' => [
                'label' => 'Свиване',
            ],

            'expand' => [
                'label' => 'Разширяване',
            ],

            'collapse_all' => [
                'label' => 'Свиване на всички',
            ],

            'expand_all' => [
                'label' => 'Разширяване на всички',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Добави връзка',
                    'unlink' => 'Премахни връзка',
                ],

                'label' => 'URL',

                'placeholder' => 'Въведи URL',

            ],

        ],

        'tools' => [
            'attach_files' => 'Прикачи файлове',
            'blockquote' => 'Цитат',
            'bold' => 'Удебелен текст',
            'bullet_list' => 'Списък с точки',
            'code_block' => 'Код',
            'h1' => 'Заглавие',
            'h2' => 'Подзаглавие',
            'h3' => 'Под-подзаглавие',
            'italic' => 'Курсив',
            'link' => 'Връзка',
            'ordered_list' => 'Номериран списък',
            'redo' => 'Повтори',
            'strike' => 'Зачертан текст',
            'underline' => 'Подчертан текст',
            'undo' => 'Отмени',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Създаване на опция',

                    'actions' => [

                        'create' => [
                            'label' => 'Създаване',
                        ],

                        'create_another' => [
                            'label' => 'Създаване и добавяне на друга',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Редакция',

                    'actions' => [

                        'save' => [
                            'label' => 'Запазване',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Да',
            'false' => 'Не',
        ],

        'loading_message' => 'Зареждане...',

        'max_items_message' => 'Само :count могат да бъдат избрани.',

        'no_search_results_message' => 'Няма намерени резултати.',

        'placeholder' => 'Избери опция',

        'searching_message' => 'Търсене...',

        'search_prompt' => 'Търсене...',

    ],

    'tags_input' => [
        'placeholder' => 'Нов таг',
    ],

];

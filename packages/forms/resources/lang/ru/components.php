<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Дублировать',
            ],

            'add' => [
                'label' => 'Добавить к :label',
            ],

            'add_between' => [
                'label' => 'Вставить между',
            ],

            'delete' => [
                'label' => 'Удалить',
            ],

            'reorder' => [
                'label' => 'Переместить',
            ],

            'move_down' => [
                'label' => 'Переместить вниз',
            ],

            'move_up' => [
                'label' => 'Переместить вверх',
            ],

            'collapse' => [
                'label' => 'Свернуть',
            ],

            'expand' => [
                'label' => 'Развернуть',
            ],

            'collapse_all' => [
                'label' => 'Свернуть все',
            ],

            'expand_all' => [
                'label' => 'Развернуть все',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Снять выделение',
            ],

            'select_all' => [
                'label' => 'Выделить все',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Отмена',
                ],

                'drag_crop' => [
                    'label' => 'Режим "кадрирование"',
                ],

                'drag_move' => [
                    'label' => 'Режим "перемещение"',
                ],

                'flip_horizontal' => [
                    'label' => 'Отразить по горизонтали',
                ],

                'flip_vertical' => [
                    'label' => 'Отразить по вертикали',
                ],

                'move_down' => [
                    'label' => 'Переместить вниз',
                ],

                'move_left' => [
                    'label' => 'Переместить влево',
                ],

                'move_right' => [
                    'label' => 'Переместить вправо',
                ],

                'move_up' => [
                    'label' => 'Переместить вверх',
                ],

                'reset' => [
                    'label' => 'Сбросить',
                ],

                'rotate_left' => [
                    'label' => 'Повернуть влево',
                ],

                'rotate_right' => [
                    'label' => 'Повернуть вправо',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Соотношение сторон :ratio',
                ],

                'save' => [
                    'label' => 'Сохранить',
                ],

                'zoom_100' => [
                    'label' => 'Увеличить до 100%',
                ],

                'zoom_in' => [
                    'label' => 'Увеличить',
                ],

                'zoom_out' => [
                    'label' => 'Уменьшить',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Высота',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Вращение',
                    'unit' => 'град',
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

                'label' => 'Соотношения сторон',

                'no_fixed' => [
                    'label' => 'Свободное',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Не рекомендуется редактировать SVG-файлы, поскольку при масштабировании может быть потеряно качество.\n Уверены, что хотите продолжить?',
                    'disabled' => 'Редактирование SVG-файлов заблокировано, поскольку при масштабировании может быть потеряно качество.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Добавить строку',
            ],

            'delete' => [
                'label' => 'Удалить строку',
            ],

            'reorder' => [
                'label' => 'Переместить строку',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Ключ',
            ],

            'value' => [
                'label' => 'Значение',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Прикрепить файлы',
            'blockquote' => 'Цитата',
            'bold' => 'Жирный',
            'bullet_list' => 'Маркировочный список',
            'code_block' => 'Код',
            'heading' => 'Заголовок',
            'italic' => 'Курсив',
            'link' => 'Ссылка',
            'ordered_list' => 'Нумерованный список',
            'redo' => 'Вернуть',
            'strike' => 'Зачеркнутый',
            'table' => 'Таблица',
            'undo' => 'Отменить',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Да',
            'false' => 'Нет',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Добавить к :label',
            ],

            'add_between' => [
                'label' => 'Добавить между',
            ],

            'delete' => [
                'label' => 'Удалить',
            ],

            'clone' => [
                'label' => 'Дублировать',
            ],

            'reorder' => [
                'label' => 'Переместить',
            ],

            'move_down' => [
                'label' => 'Переместить вниз',
            ],

            'move_up' => [
                'label' => 'Переместить вверх',
            ],

            'collapse' => [
                'label' => 'Свернуть',
            ],

            'expand' => [
                'label' => 'Развернуть',
            ],

            'collapse_all' => [
                'label' => 'Свернуть все',
            ],

            'expand_all' => [
                'label' => 'Развернуть все',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Ссылка',
                    'unlink' => 'Убрать ссылку',
                ],

                'label' => 'URL',

                'placeholder' => 'Введите URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Прикрепить файлы',
            'blockquote' => 'Цитата',
            'bold' => 'Жирный',
            'bullet_list' => 'Маркировочный список',
            'code_block' => 'Код',
            'h1' => 'Название',
            'h2' => 'Заголовок',
            'h3' => 'Подзаголовок',
            'italic' => 'Курсив',
            'link' => 'Ссылка',
            'ordered_list' => 'Нумерованный список',
            'redo' => 'Повторить',
            'strike' => 'Зачеркнутый',
            'underline' => 'Подчеркнутый',
            'undo' => 'Отменить',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Создать',

                    'actions' => [

                        'create' => [
                            'label' => 'Создать',
                        ],
                        'create_another' => [
                            'label' => 'Создать еще один',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Изменить',

                    'actions' => [

                        'save' => [
                            'label' => 'Сохранить',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Да',
            'false' => 'Нет',
        ],

        'loading_message' => 'Загрузка...',

        'max_items_message' => 'Только :count можно выбрать.',

        'no_search_results_message' => 'Нет вариантов, соответствующих вашему запросу.',

        'placeholder' => 'Выбрать вариант',

        'searching_message' => 'Поиск...',

        'search_prompt' => 'Введите текст для поиска...',

    ],

    'tags_input' => [
        'placeholder' => 'Новый тег',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Скрыть пароль',
            ],

            'show_password' => [
                'label' => 'Показать пароль',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Да',
            'false' => 'Нет',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Назад',
            ],

            'next_step' => [
                'label' => 'Далее',
            ],

        ],

    ],

];

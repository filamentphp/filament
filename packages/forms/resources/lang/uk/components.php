<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Дублювати',
            ],

            'add' => [
                'label' => 'Додати до :label',
            ],

            'add_between' => [
                'label' => 'Вставити між',
            ],

            'delete' => [
                'label' => 'Видалити',
            ],

            'reorder' => [
                'label' => 'Перемістити',
            ],

            'move_down' => [
                'label' => 'Перемістити вниз',
            ],

            'move_up' => [
                'label' => 'Перемістити вгору',
            ],

            'collapse' => [
                'label' => 'Згорнути',
            ],

            'expand' => [
                'label' => 'Розгорнути',
            ],

            'collapse_all' => [
                'label' => 'Згорнути все',
            ],

            'expand_all' => [
                'label' => 'Розгорнути все',
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
                    'label' => 'Скасувати',
                ],

                'drag_crop' => [
                    'label' => 'Режим "кадрування"',
                ],

                'drag_move' => [
                    'label' => 'Режим "переміщення"',
                ],

                'flip_horizontal' => [
                    'label' => 'Відобразити по горизонталі',
                ],

                'flip_vertical' => [
                    'label' => 'Відобразити по вертикалі',
                ],

                'move_down' => [
                    'label' => 'Перемістити вниз',
                ],

                'move_left' => [
                    'label' => 'Перемістити вліво',
                ],

                'move_right' => [
                    'label' => 'Перемістити вправо',
                ],

                'move_up' => [
                    'label' => 'Перемістити у верх',
                ],

                'reset' => [
                    'label' => 'Скинути',
                ],

                'rotate_left' => [
                    'label' => 'Повернути вліво',
                ],

                'rotate_right' => [
                    'label' => 'Повернути вправо',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Відношення сторін :ratio',
                ],

                'save' => [
                    'label' => 'Зберегти',
                ],

                'zoom_100' => [
                    'label' => 'Збільшити до 100%',
                ],

                'zoom_in' => [
                    'label' => 'Збільшити',
                ],

                'zoom_out' => [
                    'label' => 'Зменшити',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Висота',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Обертання',
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

                'label' => 'Відношення сторін',

                'no_fixed' => [
                    'label' => 'Вільне',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Додати рядок',
            ],

            'delete' => [
                'label' => 'Видалити рядок',
            ],
            'reorder' => [
                'label' => 'Перемістити рядок',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Ключ',
            ],

            'value' => [
                'label' => 'Значення',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Прикріпити файли',
            'blockquote' => 'Цитата',
            'bold' => 'Жирний',
            'bullet_list' => 'Маркувальний список',
            'code_block' => 'Код',
            'heading' => 'Заголовок',
            'italic' => 'Курсив',
            'link' => 'Посилання',
            'ordered_list' => 'Нумерований список',
            'redo' => 'Повернути',
            'strike' => 'Закреслений',
            'table' => 'Таблиця',
            'undo' => 'Скасувати',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Додати до :label',
            ],

            'delete' => [
                'label' => 'Видалити',
            ],

            'clone' => [
                'label' => 'Дублювати',
            ],

            'reorder' => [
                'label' => 'Перемістити',
            ],

            'move_down' => [
                'label' => 'Перемістити вниз',
            ],

            'move_up' => [
                'label' => 'Перемістити у верх',
            ],

            'collapse' => [
                'label' => 'Звернути',
            ],

            'expand' => [
                'label' => 'Розвернути',
            ],

            'collapse_all' => [
                'label' => 'Звернути все',
            ],

            'expand_all' => [
                'label' => 'Розвернути все',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Посилання',
                    'unlink' => 'Прибрати посилання',
                ],

                'label' => 'URL',

                'placeholder' => 'Введіть URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Прикріпити файли',
            'blockquote' => 'Цитата',
            'bold' => 'Жирний',
            'bullet_list' => 'Маркувальний список',
            'code_block' => 'Код',
            'h1' => 'Назва',
            'h2' => 'Заголовок',
            'h3' => 'Підзаголовок',
            'italic' => 'Курсів',
            'link' => 'Посилання',
            'ordered_list' => 'Нумерований список',
            'redo' => 'Повторити',
            'strike' => 'Закреслений',
            'undo' => 'Скасувати',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Створити',

                    'actions' => [

                        'create' => [
                            'label' => 'Створити',
                        ],
                        'create_another' => [
                            'label' => 'Створити ще один',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Змінити',

                    'actions' => [

                        'save' => [
                            'label' => 'Зберегти',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Так',
            'false' => 'Ні',
        ],

        'loading_message' => 'Завантаження...',

        'max_items_message' => 'Тільки :count можна вибрати.',

        'no_search_results_message' => 'Немає варіантів, які відповідають вашому запиту.',

        'placeholder' => 'Обрати варіант',

        'searching_message' => 'Пошук...',

        'search_prompt' => 'Введіть текст для пошуку...',
    ],

    'tags_input' => [
        'placeholder' => 'Новий тег',
    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Назад',
            ],

            'next_step' => [
                'label' => 'Далі',
            ],

        ],

    ],

];

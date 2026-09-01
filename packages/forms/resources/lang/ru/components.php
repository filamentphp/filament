<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Дублировать',
            ],

            'add' => [

                'label' => 'Добавить к :label',

                'modal' => [

                    'heading' => 'Добавить к :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Добавить',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Вставить между',

                'modal' => [

                    'heading' => 'Добавить к :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Добавить',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Удалить',
            ],

            'edit' => [

                'label' => 'Редактировать',

                'modal' => [

                    'heading' => 'Редактирование блока',

                    'actions' => [

                        'save' => [
                            'label' => 'Сохранить изменения',
                        ],

                    ],

                ],

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

        'file_attachments_accepted_file_types_message' => 'Загружаемые файлы должны быть типа: :values.',

        'file_attachments_max_size_message' => 'Загружаемые файлы не должны превышать :max килобайт.',

        'tools' => [
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

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Выбрать',

                'actions' => [

                    'select' => [
                        'label' => 'Выбрать',
                    ],

                ],

            ],

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

        'actions' => [

            'attach_files' => [

                'label' => 'Загрузить файл',

                'modal' => [

                    'heading' => 'Загрузка файла',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Файл',
                                'existing' => 'Заменить файл',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Альтернативный текст',
                                'existing' => 'Изменить альтернативный текст',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Вставить',
                        ],

                        'save' => [
                            'label' => 'Сохранить',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Сетка',

                'modal' => [

                    'heading' => 'Сетка',

                    'form' => [

                        'preset' => [

                            'label' => 'Предустановка',

                            'placeholder' => 'Отсутствует',

                            'options' => [
                                'two' => '2 колонки',
                                'three' => '3 колонки',
                                'four' => '4 колонки',
                                'five' => '5 колонок',
                                'two_start_third' => '2 колонки (1 / 2)',
                                'two_end_third' => '2 колонки (2 / 1)',
                                'two_start_fourth' => '2 колонки (1 / 3)',
                                'two_end_fourth' => '2 колонки (3 / 1)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Колонки',
                        ],

                        'from_breakpoint' => [

                            'label' => 'От размера экрана',

                            'options' => [
                                'default' => 'Все',
                                'sm' => 'Маленький',
                                'md' => 'Средний',
                                'lg' => 'Большой',
                                'xl' => 'Очень большой',
                                '2xl' => 'Сверхбольшой',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Две асимметричные колонки',
                        ],

                        'start_span' => [
                            'label' => 'Ширина первой колонки',
                        ],

                        'end_span' => [
                            'label' => 'Ширина второй колонки',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Изменить',

                'modal' => [

                    'heading' => 'Ссылка',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Открывать в новой вкладке',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Цвет текста',

                'modal' => [

                    'heading' => 'Цвет текста',

                    'form' => [

                        'color' => [
                            'label' => 'Цвет',

                            'options' => [
                                'slate' => 'Серый (сланец)',
                                'gray' => 'Серый',
                                'zinc' => 'Серый (цинк)',
                                'neutral' => 'Нейтральный',
                                'stone' => 'Серый (камень)',
                                'mauve' => 'Лилово-серый',
                                'olive' => 'Оливковый',
                                'mist' => 'Серый (туман)',
                                'taupe' => 'Серо-коричневый',
                                'red' => 'Красный',
                                'orange' => 'Оранжевый',
                                'amber' => 'Янтарный',
                                'yellow' => 'Жёлтый',
                                'lime' => 'Лайм',
                                'green' => 'Зелёный',
                                'emerald' => 'Изумрудный',
                                'teal' => 'Бирюзовый',
                                'cyan' => 'Голубой',
                                'sky' => 'Небесный',
                                'blue' => 'Синий',
                                'indigo' => 'Индиго',
                                'violet' => 'Фиолетовый',
                                'purple' => 'Пурпурный',
                                'fuchsia' => 'Фуксия',
                                'pink' => 'Розовый',
                                'rose' => 'Розово-красный',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'Произвольный цвет',
                        ],

                    ],

                ],

            ],
        ],

        'file_attachments_accepted_file_types_message' => 'Загружаемые файлы должны быть типа: :values.',

        'file_attachments_max_size_message' => 'Загружаемые файлы не должны превышать :max килобайт.',

        'no_merge_tag_search_results_message' => 'Нет результатов по тегам слияния.',

        'mentions' => [
            'no_options_message' => 'Нет доступных вариантов.',
            'no_search_results_message' => 'Нет вариантов, соответствующих вашему запросу.',
            'search_prompt' => 'Введите текст для поиска...',
            'searching_message' => 'Поиск...',
        ],

        'tools' => [
            'align_center' => 'По центру',
            'align_end' => 'По правому краю',
            'align_justify' => 'По ширине',
            'align_start' => 'По левому краю',
            'attach_files' => 'Прикрепить файлы',
            'blockquote' => 'Цитата',
            'bold' => 'Жирный',
            'bullet_list' => 'Маркированный список',
            'clear_formatting' => 'Очистить форматирование',
            'code' => 'Код',
            'code_block' => 'Блок кода',
            'custom_blocks' => 'Блоки',
            'details' => 'Детали',
            'h1' => 'Заголовок 1',
            'h2' => 'Заголовок 2',
            'h3' => 'Заголовок 3',
            'h4' => 'Заголовок 4',
            'h5' => 'Заголовок 5',
            'h6' => 'Заголовок 6',
            'grid' => 'Сетка',
            'grid_delete' => 'Удалить сетку',
            'highlight' => 'Выделение',
            'horizontal_rule' => 'Горизонтальная линия',
            'italic' => 'Курсив',
            'lead' => 'Вводный текст',
            'link' => 'Ссылка',
            'merge_tags' => 'Теги слияния',
            'ordered_list' => 'Нумерованный список',
            'paragraph' => 'Параграф',
            'redo' => 'Повторить',
            'small' => 'Мелкий текст',
            'strike' => 'Зачеркнутый',
            'subscript' => 'Подстрочный',
            'superscript' => 'Надстрочный',
            'table' => 'Таблица',
            'table_delete' => 'Удалить таблицу',
            'table_add_column_before' => 'Добавить столбец слева',
            'table_add_column_after' => 'Добавить столбец справа',
            'table_delete_column' => 'Удалить столбец',
            'table_add_row_before' => 'Добавить строку выше',
            'table_add_row_after' => 'Добавить строку ниже',
            'table_delete_row' => 'Удалить строку',
            'table_merge_cells' => 'Объединить ячейки',
            'table_split_cell' => 'Разделить ячейку',
            'table_toggle_header_row' => 'Переключить заголовок',
            'table_toggle_header_cell' => 'Переключить ячейку заголовка',
            'text_color' => 'Цвет текста',
            'underline' => 'Подчеркнутый',
            'undo' => 'Отменить',
        ],

        'uploading_file_message' => 'Загрузка файла...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Создать',

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

                'label' => 'Изменить',

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

        'no_options_message' => 'Нет доступных вариантов.',

        'no_search_results_message' => 'Нет вариантов, соответствующих вашему запросу.',

        'placeholder' => 'Выбрать вариант',

        'searching_message' => 'Поиск...',

        'search_prompt' => 'Введите текст для поиска...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Удалить',
            ],

        ],

        'placeholder' => 'Новый тег',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Копировать',
                'message' => 'Скопировано',
            ],

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

];

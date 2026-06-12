<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Дублювати',
            ],

            'add' => [

                'label' => 'Додати до :label',

                'modal' => [

                    'heading' => 'Додати до :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Додати',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Вставити між блоками',

                'modal' => [

                    'heading' => 'Додати до :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Додати',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Видалити',
            ],

            'edit' => [

                'label' => 'Редагувати',

                'modal' => [

                    'heading' => 'Редагувати блок',

                    'actions' => [

                        'save' => [
                            'label' => 'Зберегти зміни',
                        ],

                    ],

                ],

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
                'label' => 'Зняти виділення',
            ],

            'select_all' => [
                'label' => 'Виділити все',
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
                    'label' => 'Режим "обрізка"',
                ],

                'drag_move' => [
                    'label' => 'Режим перетягування',
                ],

                'flip_horizontal' => [
                    'label' => 'Віддзеркалити горизонтально',
                ],

                'flip_vertical' => [
                    'label' => 'Віддзеркалити вертикально',
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
                    'label' => 'Перемістити вгору',
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
                    'label' => 'Встановити співвідношення сторін :ratio',
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

                'label' => 'Співвідношення сторін',

                'no_fixed' => [
                    'label' => 'Вільно',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Редагування SVG не рекомендується, оскільки може призвести до втрати якості при масштабуванні.\n Ви впевнені, що хочете продовжити?',
                    'disabled' => 'Редагування SVG вимкнено, оскільки може призвести до втрати якості при масштабуванні.',
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
                'label' => 'Змінити порядок рядка',
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

        'file_attachments_accepted_file_types_message' => 'Файли для завантаження повинні бути наступного типу: :values.',

        'file_attachments_max_size_message' => 'Розмір завантажених файлів не повинен перевищувати :max кілобайт.',

        'tools' => [
            'attach_files' => 'Прикріпити файли',
            'blockquote' => 'Цитата',
            'bold' => 'Жирний',
            'bullet_list' => 'Маркований список',
            'code_block' => 'Блок коду',
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

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Вибрати',

                'actions' => [

                    'select' => [
                        'label' => 'Вибрати',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Так',
            'false' => 'Ні',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Додати до :label',
            ],

            'add_between' => [
                'label' => 'Вставити між',
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

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Завантажити файл',

                'modal' => [

                    'heading' => 'Завантажити файл',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Файл',
                                'existing' => 'Замінити файл',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Альтернативний текст',
                                'existing' => 'Змінити альтернативний текст',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Вставити',
                        ],

                        'save' => [
                            'label' => 'Зберегти',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Сітка',

                'modal' => [

                    'heading' => 'Сітка',

                    'form' => [

                        'preset' => [

                            'label' => 'Попередньо налаштована',

                            'placeholder' => 'Жоден',

                            'options' => [
                                'two' => 'Дві',
                                'three' => 'Три',
                                'four' => 'Чотири',
                                'five' => 'П\'ять',
                                'two_start_third' => 'Дві (Початок Третя)',
                                'two_end_third' => 'Дві (Кінець Третя)',
                                'two_start_fourth' => 'Дві (Початок Четверта)',
                                'two_end_fourth' => 'Дві (Кінець Четверта)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Колонки',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Від точки зупинки',

                            'options' => [
                                'default' => 'Всі',
                                'sm' => 'Маленький',
                                'md' => 'Середній',
                                'lg' => 'Великий',
                                'xl' => 'Дуже великий',
                                '2xl' => 'Величезний',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Дві асиметричні колонки',
                        ],

                        'start_span' => [
                            'label' => 'Початковий проміжок',
                        ],

                        'end_span' => [
                            'label' => 'Кінцевий проміжок',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Редагувати',

                'modal' => [

                    'heading' => 'Посилання',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Відкрити у новій вкладці',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Колір тексту',

                'modal' => [

                    'heading' => 'Колір тексту',

                    'form' => [

                        'color' => [
                            'label' => 'Колір',
                        ],

                        'custom_color' => [
                            'label' => 'Колір вибраний користувачем',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Файли для завантаження повинні бути наступного типу: :values.',

        'file_attachments_max_size_message' => 'Розмір завантажених файлів не повинен перевищувати :max кілобайт.',

        'no_merge_tag_search_results_message' => 'Немає результатів для тегів злиття.',

        'tools' => [
            'align_center' => 'Вирівняти по центру',
            'align_end' => 'Вирівняти праворуч',
            'align_justify' => 'Вирівняти по ширині',
            'align_start' => 'Вирівняти ліворуч',
            'attach_files' => 'Прикріпити файли',
            'blockquote' => 'Цитата',
            'bold' => 'Жирний',
            'bullet_list' => 'Маркований список',
            'clear_formatting' => 'Очистити форматування',
            'code' => 'Код',
            'code_block' => 'Блок коду',
            'custom_blocks' => 'Блоки',
            'details' => 'Деталі',
            'h1' => 'Заголовок',
            'h2' => 'Підзаголовок',
            'h3' => 'Підпідзаголовок',
            'grid' => 'Сітка',
            'grid_delete' => 'Видалити сітку',
            'highlight' => 'Виділити',
            'horizontal_rule' => 'Горизонтальна лінія',
            'italic' => 'Курсив',
            'lead' => 'Вступний текст',
            'link' => 'Посилання',
            'merge_tags' => 'Теги злиття',
            'ordered_list' => 'Нумерований список',
            'redo' => 'Повторити',
            'small' => 'Малий текст',
            'strike' => 'Закреслений',
            'subscript' => 'Нижній індекс',
            'superscript' => 'Верхній індекс',
            'table' => 'Таблиця',
            'table_delete' => 'Видалити таблицю',
            'table_add_column_before' => 'Додати стовпець перед',
            'table_add_column_after' => 'Додати стовпець після',
            'table_delete_column' => 'Видалити стовпець',
            'table_add_row_before' => 'Додати рядок вище',
            'table_add_row_after' => 'Додати рядок нижче',
            'table_delete_row' => 'Видалити рядок',
            'table_merge_cells' => 'Обʼєднати клітинки',
            'table_split_cell' => 'Розділити клітинку',
            'table_toggle_header_row' => 'Увімкнути/вимкнути заголовок',
            'text_color' => 'Колір тексту',
            'underline' => 'Підкреслений',
            'undo' => 'Скасувати',
        ],

        'uploading_file_message' => 'Завантаження файлу...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Створити',

                'modal' => [

                    'heading' => 'Створити',

                    'actions' => [

                        'create' => [
                            'label' => 'Створити',
                        ],

                        'create_another' => [
                            'label' => 'Створити і створити ще',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Редагувати',

                'modal' => [

                    'heading' => 'Редагувати',

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

        'max_items_message' => 'Можна вибрати лише :count.',

        'no_search_results_message' => 'Немає відповідних варіантів.',

        'placeholder' => 'Виберіть варіант',

        'searching_message' => 'Пошук...',

        'search_prompt' => 'Введіть текст для пошуку...',

    ],

    'tags_input' => [
        'placeholder' => 'Новий тег',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Скопіювати',
                'message' => 'Скопійовано',
            ],

            'hide_password' => [
                'label' => 'Приховати пароль',
            ],

            'show_password' => [
                'label' => 'Показати пароль',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Так',
            'false' => 'Ні',
        ],

    ],

];

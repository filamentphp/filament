<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Нусхабардорӣ',
            ],

            'add' => [

                'label' => 'Илова ба :label',

                'modal' => [

                    'heading' => 'Илова ба :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Илова кардан',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Илова байни',

                'modal' => [

                    'heading' => 'Илова ба :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Илова кардан',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Нест кардан',
            ],

            'edit' => [

                'label' => 'Таҳрир кардан',

                'modal' => [

                    'heading' => 'Таҳрири блок',

                    'actions' => [

                        'save' => [
                            'label' => 'Нигоҳ доштани тағйирот',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Ҷойивазкунӣ',
            ],

            'move_down' => [
                'label' => 'Ба поён гузарондан',
            ],

            'move_up' => [
                'label' => 'Ба боло гузарондан',
            ],

            'collapse' => [
                'label' => 'Пинҳон кардан',
            ],

            'expand' => [
                'label' => 'Кушодан',
            ],

            'collapse_all' => [
                'label' => 'Ҳамаро пинҳон кардан',
            ],

            'expand_all' => [
                'label' => 'Ҳамаро кушодан',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Бекор кардани интихоб',
            ],

            'select_all' => [
                'label' => 'Интихоби ҳама',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Бекор кардан',
                ],

                'drag_crop' => [
                    'label' => 'Ҳолати "буридан"',
                ],

                'drag_move' => [
                    'label' => 'Ҳолати "ҷойивазкунӣ"',
                ],

                'flip_horizontal' => [
                    'label' => 'Гардонидани уфуқӣ',
                ],

                'flip_vertical' => [
                    'label' => 'Гардонидани амудӣ',
                ],

                'move_down' => [
                    'label' => 'Ба поён гузарондан',
                ],

                'move_left' => [
                    'label' => 'Ба чап гузарондан',
                ],

                'move_right' => [
                    'label' => 'Ба рост гузарондан',
                ],

                'move_up' => [
                    'label' => 'Ба боло гузарондан',
                ],

                'reset' => [
                    'label' => 'Аз нав барқарор кардан',
                ],

                'rotate_left' => [
                    'label' => 'Ба чап тоб додан',
                ],

                'rotate_right' => [
                    'label' => 'Ба рост тоб додан',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Таносуби тарафҳо :ratio',
                ],

                'save' => [
                    'label' => 'Нигоҳ доштан',
                ],

                'zoom_100' => [
                    'label' => 'Ба 100% калон кардан',
                ],

                'zoom_in' => [
                    'label' => 'Калон кардан',
                ],

                'zoom_out' => [
                    'label' => 'Хурд кардан',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Баландӣ',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Гардиш',
                    'unit' => 'дараҷа',
                ],

                'width' => [
                    'label' => 'Паҳноӣ',
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

                'label' => 'Таносуби тарафҳо',

                'no_fixed' => [
                    'label' => 'Озод',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Таҳрири файлҳои SVG тавсия дода намешавад, зеро ҳангоми миқёсгузорӣ сифат метавонад гум шавад.\nОё мутмаин ҳастед, ки мехоҳед идома диҳед?',
                    'disabled' => 'Таҳрири файлҳои SVG баста шудааст, зеро ҳангоми миқёсгузорӣ сифат метавонад гум шавад.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Илова кардани сатр',
            ],

            'delete' => [
                'label' => 'Нест кардани сатр',
            ],

            'reorder' => [
                'label' => 'Ҷойивазкунии сатр',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Калид',
            ],

            'value' => [
                'label' => 'Қимат',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Файлҳои боршаванда бояд аз навъи: :values бошанд.',

        'file_attachments_max_size_message' => 'Андозаи файлҳои боршаванда набояд аз :max килобайт зиёд бошад.',

        'tools' => [
            'attach_files' => 'Замима кардани файлҳо',
            'blockquote' => 'Иқтибос',
            'bold' => 'Ғафс',
            'bullet_list' => 'Рӯйхати нишонадор',
            'code_block' => 'Код',
            'heading' => 'Сарлавҳа',
            'italic' => 'Моил',
            'link' => 'Пайванд',
            'ordered_list' => 'Рӯйхати рақамдор',
            'redo' => 'Такрор',
            'strike' => 'Хатзада',
            'table' => 'Ҷадвал',
            'undo' => 'Бекор кардан',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Интихоб кардан',

                'actions' => [

                    'select' => [
                        'label' => 'Интихоб кардан',
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
                'label' => 'Илова ба :label',
            ],

            'add_between' => [
                'label' => 'Илова байни',
            ],

            'delete' => [
                'label' => 'Нест кардан',
            ],

            'clone' => [
                'label' => 'Нусхабардорӣ',
            ],

            'reorder' => [
                'label' => 'Ҷойивазкунӣ',
            ],

            'move_down' => [
                'label' => 'Ба поён гузарондан',
            ],

            'move_up' => [
                'label' => 'Ба боло гузарондан',
            ],

            'collapse' => [
                'label' => 'Пинҳон кардан',
            ],

            'expand' => [
                'label' => 'Кушодан',
            ],

            'collapse_all' => [
                'label' => 'Ҳамаро пинҳон кардан',
            ],

            'expand_all' => [
                'label' => 'Ҳамаро кушодан',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Боргузории файл',

                'modal' => [

                    'heading' => 'Боргузории файл',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Файл',
                                'existing' => 'Иваз кардани файл',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Матни алтернативӣ',
                                'existing' => 'Тағйир додани матни алтернативӣ',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Ворид кардан',
                        ],

                        'save' => [
                            'label' => 'Нигоҳ доштан',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Шабака',

                'modal' => [

                    'heading' => 'Шабака',

                    'form' => [

                        'preset' => [

                            'label' => 'Танзимоти пешакӣ',

                            'placeholder' => 'Мавҷуд нест',

                            'options' => [
                                'two' => '2 сутун',
                                'three' => '3 сутун',
                                'four' => '4 сутун',
                                'five' => '5 сутун',
                                'two_start_third' => '2 сутун (1 / 2)',
                                'two_end_third' => '2 сутун (2 / 1)',
                                'two_start_fourth' => '2 сутун (1 / 3)',
                                'two_end_fourth' => '2 сутун (3 / 1)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Сутунҳо',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Аз андозаи экран',

                            'options' => [
                                'default' => 'Ҳама',
                                'sm' => 'Хурд',
                                'md' => 'Миёна',
                                'lg' => 'Калон',
                                'xl' => 'Хеле калон',
                                '2xl' => 'Бениҳоят калон',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Ду сутуни нобаробар',
                        ],

                        'start_span' => [
                            'label' => 'Паҳноии сутуни аввал',
                        ],

                        'end_span' => [
                            'label' => 'Паҳноии сутуни дуюм',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Таҳрир кардан',

                'modal' => [

                    'heading' => 'Пайванд',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Дар варақаи нав кушода шавад',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Ранги матн',

                'modal' => [

                    'heading' => 'Ранги матн',

                    'form' => [

                        'color' => [
                            'label' => 'Ранг',

                            'options' => [
                                'slate' => 'Хокистарӣ (сланец)',
                                'gray' => 'Хокистарӣ',
                                'zinc' => 'Хокистарӣ (руҳ)',
                                'neutral' => 'Бетараф',
                                'stone' => 'Хокистарӣ (санг)',
                                'mauve' => 'Хокистарии бунафштоб',
                                'olive' => 'Зайтунӣ',
                                'mist' => 'Хокистарии туманӣ',
                                'taupe' => 'Хокистарии қаҳваранг',
                                'red' => 'Сурх',
                                'orange' => 'Норанҷӣ',
                                'amber' => 'Қаҳрабоӣ',
                                'yellow' => 'Зард',
                                'lime' => 'Лаймӣ',
                                'green' => 'Сабз',
                                'emerald' => 'Зумуррадӣ',
                                'teal' => 'Фирӯзагӣ',
                                'cyan' => 'Кабуди равшан',
                                'sky' => 'Осмонӣ',
                                'blue' => 'Кабуд',
                                'indigo' => 'Индиго',
                                'violet' => 'Бунафш',
                                'purple' => 'Арғувонӣ',
                                'fuchsia' => 'Фуксия',
                                'pink' => 'Гулобӣ',
                                'rose' => 'Сурхи гулобӣ',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'Ранги ихтиёрӣ',
                        ],

                    ],

                ],

            ],
        ],

        'file_attachments_accepted_file_types_message' => 'Файлҳои боршаванда бояд аз навъи: :values бошанд.',

        'file_attachments_max_size_message' => 'Андозаи файлҳои боршаванда набояд аз :max килобайт зиёд бошад.',

        'no_merge_tag_search_results_message' => 'Аз рӯи барчаспҳои ҳамгироӣ натиҷа ёфт нашуд.',

        'mentions' => [
            'no_options_message' => 'Ягон интихоб дастрас нест.',
            'no_search_results_message' => 'Ягон интихобе, ки ба дархости шумо мувофиқат кунад, ёфт нашуд.',
            'search_prompt' => 'Барои ҷустуҷӯ матнро ворид кунед...',
            'searching_message' => 'Ҷустуҷӯ...',
        ],

        'tools' => [
            'align_center' => 'Марказ',
            'align_end' => 'Аз тарафи рост',
            'align_justify' => 'Баробаркунӣ аз рӯи паҳноӣ',
            'align_start' => 'Аз тарафи чап',
            'attach_files' => 'Замима кардани файлҳо',
            'blockquote' => 'Иқтибос',
            'bold' => 'Ғафс',
            'bullet_list' => 'Рӯйхати нишонадор',
            'clear_formatting' => 'Тоза кардани формат',
            'code' => 'Код',
            'code_block' => 'Блоки код',
            'custom_blocks' => 'Блокҳо',
            'details' => 'Тафсилот',
            'h1' => 'Сарлавҳаи 1',
            'h2' => 'Сарлавҳаи 2',
            'h3' => 'Сарлавҳаи 3',
            'h4' => 'Сарлавҳаи 4',
            'h5' => 'Сарлавҳаи 5',
            'h6' => 'Сарлавҳаи 6',
            'grid' => 'Шабака',
            'grid_delete' => 'Нест кардани шабака',
            'highlight' => 'Ҷудо кардан',
            'horizontal_rule' => 'Хати уфуқӣ',
            'italic' => 'Моил',
            'lead' => 'Матни муқаддимавӣ',
            'link' => 'Пайванд',
            'merge_tags' => 'Барчаспҳои ҳамгироӣ',
            'ordered_list' => 'Рӯйхати рақамдор',
            'paragraph' => 'Параграф',
            'redo' => 'Такрор',
            'small' => 'Матни хурд',
            'strike' => 'Хатзада',
            'subscript' => 'Зерсатрӣ',
            'superscript' => 'Болосатрӣ',
            'table' => 'Ҷадвал',
            'table_delete' => 'Нест кардани ҷадвал',
            'table_add_column_before' => 'Илова кардани сутун аз чап',
            'table_add_column_after' => 'Илова кардани сутун аз рост',
            'table_delete_column' => 'Нест кардани сутун',
            'table_add_row_before' => 'Илова кардани сатр аз боло',
            'table_add_row_after' => 'Илова кардани сатр аз поён',
            'table_delete_row' => 'Нест кардани сатр',
            'table_merge_cells' => 'Якҷоя кардани чашмакҳо',
            'table_split_cell' => 'Ҷудо кардани чашмак',
            'table_toggle_header_row' => 'Иваз кардани сатри сарлавҳа',
            'table_toggle_header_cell' => 'Иваз кардани чашмаки сарлавҳа',
            'text_color' => 'Ранги матн',
            'underline' => 'Зерхатдор',
            'undo' => 'Бекор кардан',
        ],

        'uploading_file_message' => 'Боргузории файл...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Эҷод кардан',

                'modal' => [

                    'heading' => 'Эҷод кардан',

                    'actions' => [

                        'create' => [
                            'label' => 'Эҷод кардан',
                        ],

                        'create_another' => [
                            'label' => 'Боз якто эҷод кардан',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Таҳрир кардан',

                'modal' => [

                    'heading' => 'Таҳрир кардан',

                    'actions' => [

                        'save' => [
                            'label' => 'Нигоҳ доштан',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Ҳа',
            'false' => 'Не',
        ],

        'loading_message' => 'Боркунӣ...',

        'max_items_message' => 'Танҳо :count интихоб кардан мумкин аст.',

        'no_options_message' => 'Ягон интихоб дастрас нест.',

        'no_search_results_message' => 'Ягон интихобе, ки ба дархости шумо мувофиқат кунад, ёфт нашуд.',

        'placeholder' => 'Интихоб кунед',

        'searching_message' => 'Ҷустуҷӯ...',

        'search_prompt' => 'Барои ҷустуҷӯ матнро ворид кунед...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Нест кардан',
            ],

        ],

        'placeholder' => 'Барчаспи нав',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Нусхабардорӣ',
                'message' => 'Нусхабардорӣ шуд',
            ],

            'hide_password' => [
                'label' => 'Пинҳон кардани парол',
            ],

            'show_password' => [
                'label' => 'Намоиш додани парол',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Ҳа',
            'false' => 'Не',
        ],

    ],

];

<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Хуулбарлах',
            ],

            'add' => [

                'label' => 'Нэмэх :label',

                'modal' => [

                    'heading' => 'Нэмэх :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Нэмэх',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Блок хооронд нэмэх',

                'modal' => [

                    'heading' => 'Нэмэх :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Нэмэх',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Устгах',
            ],

            'edit' => [

                'label' => 'Засах',

                'modal' => [

                    'heading' => 'Блокыг засах',

                    'actions' => [

                        'save' => [
                            'label' => 'Өөрчлөлтийг хадгалах',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Зөөх',
            ],

            'move_down' => [
                'label' => 'Доош зөөх',
            ],

            'move_up' => [
                'label' => 'Дээш зөөх',
            ],

            'collapse' => [
                'label' => 'Хураах',
            ],

            'expand' => [
                'label' => 'Задлах',
            ],

            'collapse_all' => [
                'label' => 'Бүгдийг хураах',
            ],

            'expand_all' => [
                'label' => 'Бүгдийг задлах',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Бүх сонголтыг цуцлах',
            ],

            'select_all' => [
                'label' => 'Бүгдийг сонгох',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Цуцлах',
                ],

                'drag_crop' => [
                    'label' => 'Чирэх горим "тайрах"',
                ],

                'drag_move' => [
                    'label' => 'Чирэх горим "зөөх"',
                ],

                'flip_horizontal' => [
                    'label' => 'Хөндлөх эргүүлэх',
                ],

                'flip_vertical' => [
                    'label' => 'Босоо эргүүлэх',
                ],

                'move_down' => [
                    'label' => 'Зургийг доош зөөх',
                ],

                'move_left' => [
                    'label' => 'Зургийг зүүн тийш зөөх',
                ],

                'move_right' => [
                    'label' => 'Зургийг баруун тийш зөөх',
                ],

                'move_up' => [
                    'label' => 'Зургийг дээш зөөх',
                ],

                'reset' => [
                    'label' => 'Дахин эхлэх',
                ],

                'rotate_left' => [
                    'label' => 'Нар буруу эргүүлэх',
                ],

                'rotate_right' => [
                    'label' => 'Нар зөв эргүүлэх',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Харьцаа сонгох :ratio',
                ],

                'save' => [
                    'label' => 'Хадгалах',
                ],

                'zoom_100' => [
                    'label' => 'Зургийг 100% харах',
                ],

                'zoom_in' => [
                    'label' => 'Томруулах',
                ],

                'zoom_out' => [
                    'label' => 'Жижигрүүлэх',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Өндөр',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Эргүүлэх',
                    'unit' => 'градус',
                ],

                'width' => [
                    'label' => 'Өргөн',
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

                'label' => 'Харьцаа',

                'no_fixed' => [
                    'label' => 'Чөлөөт',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG файлуудыг засварлахыг зөвлөдөггүй, учир нь хэмжээг өөрчлөх үед чанар алдагддаг.\n Та үргэлжлүүлэх үү?',
                    'disabled' => 'SVG файлуудыг засварлах боломжийг идэвхгүй болгосон, учир нь хэмжээг өөрчлөх үед чанар алдагддаг.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Мөр нэмэх',
            ],

            'delete' => [
                'label' => 'Мөр устгах',
            ],

            'reorder' => [
                'label' => 'Мөрийн дарааллыг өөрчлөх',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Түлхүүр',
            ],

            'value' => [
                'label' => 'Утга',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Файл хавсаргах',
            'blockquote' => 'Ишлэл',
            'bold' => 'Өргөн',
            'bullet_list' => 'Цэгтэй жагсаалт',
            'code_block' => 'Кодын блок',
            'heading' => 'Толгой',
            'italic' => 'Налуу',
            'link' => 'Холбоос',
            'ordered_list' => 'Дугаарлалттай жагсаалт',
            'redo' => 'Сэргээх',
            'strike' => 'Зураас',
            'table' => 'Хүснэгт',
            'undo' => 'Буцаах',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Тийм',
            'false' => 'Үгүй',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Нэмэх :label',
            ],

            'add_between' => [
                'label' => 'Завсарт нь нэмэх',
            ],

            'delete' => [
                'label' => 'Устгах',
            ],

            'clone' => [
                'label' => 'Хуулбарлах',
            ],

            'reorder' => [
                'label' => 'Зөөх',
            ],

            'move_down' => [
                'label' => 'Доош зөөх',
            ],

            'move_up' => [
                'label' => 'Дээш зөөх',
            ],

            'collapse' => [
                'label' => 'Хураах',
            ],

            'expand' => [
                'label' => 'Задлах',
            ],

            'collapse_all' => [
                'label' => 'Бүгдийг хураах',
            ],

            'expand_all' => [
                'label' => 'Бүгдийг задлах',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Холбоос',
                    'unlink' => 'Холбоосыг цуцлах',
                ],

                'label' => 'Вэб хаяг (URL)',

                'placeholder' => 'Вэб хаяг (URL) оруулах ',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Файл хавсаргах',
            'blockquote' => 'Ишлэл',
            'bold' => 'Өргөн',
            'bullet_list' => 'Цэгтэй жагсаалт',
            'code_block' => 'Кодын блок',
            'h1' => 'Гарчиг',
            'h2' => 'Толгой',
            'h3' => 'Дэд толгой',
            'italic' => 'Налуу',
            'link' => 'Холбоос',
            'ordered_list' => 'Дугаарлалттай жагсаалт',
            'redo' => 'Сэргээх',
            'strike' => 'Зураас',
            'underline' => 'Доогуур зураас',
            'undo' => 'Буцаах',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Үүсгэх',

                    'actions' => [

                        'create' => [
                            'label' => 'Үүсгэх',
                        ],

                        'create_another' => [
                            'label' => 'Үүсгээд & дахин шинийг эхлэх',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Засах',

                    'actions' => [

                        'save' => [
                            'label' => 'Хадгалах',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Тийм',
            'false' => 'Үгүй',
        ],

        'loading_message' => 'Ачааллаж байна...',

        'max_items_message' => 'Зөвхөн :count сонгох боломжтой.',

        'no_search_results_message' => 'Хайлтанд тохирох сонголт олдсонгүй.',

        'placeholder' => 'Сонголтоо хийнэ үү',

        'searching_message' => 'Хайж байна...',

        'search_prompt' => 'Хайх үгээ бичнэ үү...',

    ],

    'tags_input' => [
        'placeholder' => 'Шинэ шошго',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Нууц үгийг нуух',
            ],

            'show_password' => [
                'label' => 'Нууц үгийг харуулах',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Тийм',
            'false' => 'Үгүй',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Буцах',
            ],

            'next_step' => [
                'label' => 'Дараагийн',
            ],

        ],

    ],

];

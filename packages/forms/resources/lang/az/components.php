<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klonla',
            ],

            'add' => [
                'label' => ':label əlavə et',

                'modal' => [

                    'heading' => ':label əlavə et',

                    'actions' => [

                        'add' => [
                            'label' => 'əlavə et',
                        ],

                    ],

                ],
            ],

            'add_between' => [
                'label' => 'Bloklar arasına əlavə et',

                'modal' => [

                    'heading' => ':label əlavə et',

                    'actions' => [

                        'add' => [
                            'label' => 'əlavə et',
                        ],

                    ],

                ],
            ],

            'delete' => [
                'label' => 'Sil',
            ],

            'edit' => [

                'label' => 'Dəyişdir',

                'modal' => [

                    'heading' => 'Bloku redaktə et',

                    'actions' => [

                        'save' => [
                            'label' => 'Dəyişiklikləri yadda saxla',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Sırala',
            ],

            'move_down' => [
                'label' => 'Aşağı hərəkət etdir',
            ],

            'move_up' => [
                'label' => 'Yuxarı hərəkət etdir',
            ],

            'collapse' => [
                'label' => 'Kiçilt',
            ],

            'expand' => [
                'label' => 'Genişlət',
            ],

            'collapse_all' => [
                'label' => 'Hamısını kiçilt',
            ],

            'expand_all' => [
                'label' => 'Hamısını genişlət',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Heçbirini seçmə',
            ],

            'select_all' => [
                'label' => 'Hamısını seç',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'İmtina',
                ],

                'drag_crop' => [
                    'label' => 'Kəsmə moduna sürüşdürün',
                ],

                'drag_move' => [
                    'label' => 'Daşıma moduna sürüşdürün',
                ],

                'flip_horizontal' => [
                    'label' => 'Şəkili üfüqi çevir',
                ],

                'flip_vertical' => [
                    'label' => 'Şəkili şaquli çevir',
                ],

                'move_down' => [
                    'label' => 'Şəkili aşağı hərəkət etdir',
                ],

                'move_left' => [
                    'label' => 'Şəkili sola hərəkət etdir',
                ],

                'move_right' => [
                    'label' => 'Şəkili sağa hərəkət etdir',
                ],

                'move_up' => [
                    'label' => 'Şəkili yuxarı hərəkət etdir',
                ],

                'reset' => [
                    'label' => 'Sıfırla',
                ],

                'rotate_left' => [
                    'label' => 'Şəkili sola çevir',
                ],

                'rotate_right' => [
                    'label' => 'Şəkili sağa çevir',
                ],

                'set_aspect_ratio' => [
                    'label' => 'En uzun nisbətini :ratio et',
                ],

                'save' => [
                    'label' => 'Yadda saxla',
                ],

                'zoom_100' => [
                    'label' => 'Şəkili %100 yaxınlaşdır',
                ],

                'zoom_in' => [
                    'label' => 'Yaxınlaşdır',
                ],

                'zoom_out' => [
                    'label' => 'Uzaqlaşdır',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Yüksəklik',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Çevirmə',
                    'unit' => '°',
                ],

                'width' => [
                    'label' => 'Genişlik',
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

                'label' => 'En uzun nisbətləri',

                'no_fixed' => [
                    'label' => 'Sərbəst',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG fayllarını redaktə etmək tövsiyə edilmir, çünki miqyaslandırılarkən keyfiyyət itkisinə səbəb ola bilər.\n Davam etmək istədiyinizə əminsiniz.',
                    'disabled' => 'SVG fayllarını redaktə etmək deaktiv edilib, çünki miqyaslandırılarkən keyfiyyət itkisinə səbəb ola bilər.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Sətir əlavə et',
            ],

            'delete' => [
                'label' => 'Sətir sil',
            ],

            'reorder' => [
                'label' => 'Sətir sırala',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Açar',
            ],

            'value' => [
                'label' => 'Dəyər',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Fayl əlavə et',
            'blockquote' => 'Sitat',
            'bold' => 'Qalın',
            'bullet_list' => 'List',
            'code_block' => 'Kod bloku',
            'heading' => 'Başlıq',
            'italic' => 'Əyik',
            'link' => 'Keçid',
            'ordered_list' => 'Nömrəli list',
            'redo' => 'Təkrarla',
            'strike' => 'Üstü xətli',
            'table' => 'Cədvəl',
            'undo' => 'Geri al',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Bəli',
            'false' => 'Xeyr',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => ':label\'e əlavə et',
            ],

            'add_between' => [
                'label' => 'Arasına daxil et',
            ],

            'delete' => [
                'label' => 'Sil',
            ],

            'clone' => [
                'label' => 'Klonla',
            ],

            'reorder' => [
                'label' => 'Sırala',
            ],

            'move_down' => [
                'label' => 'Aşağı hərəkət etdir',
            ],

            'move_up' => [
                'label' => 'Yuxarı hərəkət etdir',
            ],

            'collapse' => [
                'label' => 'Kiçilt',
            ],

            'expand' => [
                'label' => 'Genişlət',
            ],

            'collapse_all' => [
                'label' => 'Hamısını kiçilt',
            ],

            'expand_all' => [
                'label' => 'Hamısını genişlət',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Keçid',
                    'unlink' => 'Keçidi yığışdır',
                ],

                'label' => 'URL',

                'placeholder' => 'Bir URL daxil edin',

            ],

        ],

        'tools' => [
            'attach_files' => 'Fayl əlavə et',
            'blockquote' => 'Sitat',
            'bold' => 'Qalın',
            'bullet_list' => 'Sırasız list',
            'code_block' => 'Kod bloku',
            'h1' => 'Başlıq',
            'h2' => 'Başlıq 2',
            'h3' => 'Alt başlıq',
            'italic' => 'Əyik',
            'link' => 'Keçid',
            'ordered_list' => 'Sıralı list',
            'redo' => 'Təkrarla',
            'strike' => 'Üstü xətli',
            'underline' => 'Altı xətli',
            'undo' => 'Geri al',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Yarat',

                    'actions' => [

                        'create' => [
                            'label' => 'Yarat',
                        ],

                        'create_another' => [
                            'label' => 'Yarat & başqasını yarat',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Redaktə et',

                    'actions' => [

                        'save' => [
                            'label' => 'Yadda saxla',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Hə',
            'false' => 'Yox',
        ],

        'loading_message' => 'Yüklənir...',

        'max_items_message' => 'Sadəcə :count seçiləbilər.',

        'no_search_results_message' => 'Axtarışa uyğun seçim yoxdur.',

        'placeholder' => 'Bir seçim seçin',

        'searching_message' => 'Axtarılır...',

        'search_prompt' => 'Axtarmaq üçün yazmağa başlayın...',

    ],

    'tags_input' => [
        'placeholder' => 'Yeni etiket',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Şifrəni gizlət',
            ],

            'show_password' => [
                'label' => 'Şifrəni göstər',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Bəli',
            'false' => 'Xeyr',
        ],

    ],

];

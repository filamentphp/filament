<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klon',
            ],

            'add' => [

                'label' => 'Tambah ke :label',

                'modal' => [

                    'heading' => 'Tambah ke :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Tambah',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Masukkan',

                'modal' => [

                    'heading' => 'Add to :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Tambah',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Padam',
            ],

            'edit' => [

                'label' => 'Sunting',

                'modal' => [

                    'heading' => 'Sunting blok',

                    'actions' => [

                        'save' => [
                            'label' => 'Simpan perubahan',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Pindah',
            ],

            'move_down' => [
                'label' => 'Pindah ke bawah',
            ],

            'move_up' => [
                'label' => 'Pindah ke atas',
            ],

            'collapse' => [
                'label' => 'Tutup',
            ],

            'expand' => [
                'label' => 'Buka',
            ],

            'collapse_all' => [
                'label' => 'Tutup semua',
            ],

            'expand_all' => [
                'label' => 'Buka semua',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Nyahpilih semua',
            ],

            'select_all' => [
                'label' => 'Pilih semua',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Batal',
                ],

                'drag_crop' => [
                    'label' => 'Mod seret "crop"',
                ],

                'drag_move' => [
                    'label' => 'Mod seret "move"',
                ],

                'flip_horizontal' => [
                    'label' => 'Balikkan imej mendatar',
                ],

                'flip_vertical' => [
                    'label' => 'Balikkan imej menegak',
                ],

                'move_down' => [
                    'label' => 'Gerakkan imej ke bawah',
                ],

                'move_left' => [
                    'label' => 'Alihkan imej ke kiri',
                ],

                'move_right' => [
                    'label' => 'Alihkan imej ke kanan',
                ],

                'move_up' => [
                    'label' => 'Alihkan imej ke atas',
                ],

                'reset' => [
                    'label' => 'Tetapkan semula',
                ],

                'rotate_left' => [
                    'label' => 'Putar imej ke kiri',
                ],

                'rotate_right' => [
                    'label' => 'Putar imej ke kanan',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Tetapkan nisbah bidang kepada :ratio',
                ],

                'save' => [
                    'label' => 'Simpan',
                ],

                'zoom_100' => [
                    'label' => 'Zum imej kepada 100%',
                ],

                'zoom_in' => [
                    'label' => 'Zum masuk',
                ],

                'zoom_out' => [
                    'label' => 'Zum keluar',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Ketinggian',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Putaran',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Lebar',
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

                'label' => 'Nisbah aspek',

                'no_fixed' => [
                    'label' => 'Bebas',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Mengedit fail SVG tidak disyorkan kerana ia boleh mengakibatkan kehilangan kualiti semasa penskalaan.\n Adakah anda pasti mahu meneruskan?',
                    'disabled' => 'Mengedit fail SVG dilumpuhkan kerana ia boleh menyebabkan kehilangan kualiti apabila penskalaan.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Tambah Baris',
            ],

            'delete' => [
                'label' => 'Padam Baris',
            ],

            'reorder' => [
                'label' => 'Susun Baris',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Kunci',
            ],

            'value' => [
                'label' => 'Nilai',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Lampirkan fail',
            'blockquote' => 'Blok petikan',
            'bold' => 'Bold',
            'bullet_list' => 'Bullet list',
            'code_block' => 'Blok kod',
            'heading' => 'Tajuk',
            'italic' => 'Italic',
            'link' => 'Pautan',
            'ordered_list' => 'Senarai bernombor',
            'redo' => 'Buat semula',
            'strike' => 'Strikethrough',
            'table' => 'Jadual',
            'undo' => 'Buat asal',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Pilih',

                'actions' => [

                    'select' => [
                        'label' => 'Pilih',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Ya',
            'false' => 'Tidak',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Tambah ke :label',
            ],

            'add_between' => [
                'label' => 'Masukkan antara',
            ],

            'delete' => [
                'label' => 'Padam',
            ],

            'clone' => [
                'label' => 'Klon',
            ],

            'reorder' => [
                'label' => 'Pindah',
            ],

            'move_down' => [
                'label' => 'Pindah ke atas',
            ],

            'move_up' => [
                'label' => 'Pindah ke bawah',
            ],

            'collapse' => [
                'label' => 'Tutup',
            ],

            'expand' => [
                'label' => 'Buka',
            ],

            'collapse_all' => [
                'label' => 'Tutup semua',
            ],

            'expand_all' => [
                'label' => 'Buka semua',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Lampirkan fail',

                'modal' => [

                    'heading' => 'Lampirkan fail',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Fail baru',
                                'existing' => 'Fail sedia ada',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alt text baru',
                                'existing' => 'Tukar alt text',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Masukkan',
                        ],

                        'save' => [
                            'label' => 'Simpan',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Sunting',

                'modal' => [

                    'heading' => 'Pautan',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Buka pautan dalam tab baru',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Tiada hasil carian yang sepadan dengan tag penggabungan anda.',

        'tools' => [
            'attach_files' => 'Lampirkan fail',
            'blockquote' => 'Blockquote',
            'bold' => 'Bold',
            'bullet_list' => 'Bullet list',
            'code_block' => 'Blok kod',
            'custom_blocks' => 'Blok tersuai',
            'h1' => 'Title',
            'h2' => 'Heading',
            'h3' => 'Subheading',
            'italic' => 'Italic',
            'link' => 'Pautan',
            'merge_tags' => 'Tag penggabungan',
            'ordered_list' => 'Senarai bernombor',
            'redo' => 'Buat semula',
            'strike' => 'Strikethrough',
            'subscript' => 'Subscript',
            'superscript' => 'Superscript',
            'underline' => 'Garis bawah',
            'undo' => 'Buat asal',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [
                'label' => 'Cipta',

                'modal' => [

                    'heading' => 'Cipta',

                    'actions' => [

                        'create' => [
                            'label' => 'Cipta',
                        ],

                        'create_another' => [
                            'label' => 'Cipta dan cipta yang lain',
                        ],

                    ],

                ],

            ],

            'edit_option' => [
                'label' => 'Sunting',

                'modal' => [

                    'heading' => 'Sunting',

                    'actions' => [

                        'save' => [
                            'label' => 'Simpan',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Ya',
            'false' => 'Tidak',
        ],

        'loading_message' => 'Memuatkan...',

        'max_items_message' => 'Hanya :count boleh dipilih.',

        'no_search_results_message' => 'Tiada pilihan yang sepadan dengan carian anda.',

        'placeholder' => 'Pilih satu pilihan',

        'searching_message' => 'Mencari...',

        'search_prompt' => 'Mula menaip untuk mencari...',

    ],

    'tags_input' => [
        'placeholder' => 'Tag baru',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Sembunyikan kata laluan',
            ],

            'show_password' => [
                'label' => 'Tunjukkan kata laluan',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Ya',
            'false' => 'Tidak',
        ],

    ],

];

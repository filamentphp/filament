<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Duplikat',
            ],

            'add' => [

                'label' => 'Tambahkan ke :label',

                'modal' => [

                    'heading' => 'Tambahkan ke :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Tambah',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Sisipkan di antara blok',

                'modal' => [

                    'heading' => 'Tambahkan ke :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Tambah',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Hapus',
            ],

            'edit' => [

                'label' => 'Ubah',

                'modal' => [

                    'heading' => 'Ubah blok',

                    'actions' => [

                        'save' => [
                            'label' => 'Simpan perubahan',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Pindahkan',
            ],

            'move_down' => [
                'label' => 'Turunkan',
            ],

            'move_up' => [
                'label' => 'Naikkan',
            ],

            'collapse' => [
                'label' => 'Sembunyikan',
            ],

            'expand' => [
                'label' => 'Tampilkan',
            ],

            'collapse_all' => [
                'label' => 'Sembunyikan semua',
            ],

            'expand_all' => [
                'label' => 'Tampilkan semua',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Batalkan semua pilihan',
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
                    'label' => 'Mode "potong"',
                ],

                'drag_move' => [
                    'label' => 'Mode "geser"',
                ],

                'flip_horizontal' => [
                    'label' => 'Balik gambar secara horizontal',
                ],

                'flip_vertical' => [
                    'label' => 'Balik gambar secara vertikal',
                ],

                'move_down' => [
                    'label' => 'Geser gambar ke bawah',
                ],

                'move_left' => [
                    'label' => 'Geser gambar ke kiri',
                ],

                'move_right' => [
                    'label' => 'Geser gambar ke kanan',
                ],

                'move_up' => [
                    'label' => 'Geser gambar ke atas',
                ],

                'reset' => [
                    'label' => 'Kembalikan',
                ],

                'rotate_left' => [
                    'label' => 'Putar gambar ke kiri',
                ],

                'rotate_right' => [
                    'label' => 'Putar gambar ke kanan',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Tentukan aspek rasio ke :ratio',
                ],

                'save' => [
                    'label' => 'Simpan',
                ],

                'zoom_100' => [
                    'label' => 'Perbesar ke 100%',
                ],

                'zoom_in' => [
                    'label' => 'Perbesar',
                ],

                'zoom_out' => [
                    'label' => 'Perkecil',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Tinggi',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Putar',
                    'unit' => 'derajat',
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

                'label' => 'Aspek rasio',

                'no_fixed' => [
                    'label' => 'Bebas',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Mengedit file SVG tidak disarankan karena dapat mengakibatkan penurunan kualitas saat melakukan penskalaan.\n Apakah Anda yakin ingin melanjutkan?',
                    'disabled' => 'Pengeditan file SVG dinonaktifkan karena dapat mengakibatkan penurunan kualitas saat melakukan penskalaan.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Tambahkan baris',
            ],

            'delete' => [
                'label' => 'Hapus baris',
            ],

            'reorder' => [
                'label' => 'Ubah urutan baris',
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

        'file_attachments_accepted_file_types_message' => 'Berkas yang diunggah harus bertipe: :values.',

        'file_attachments_max_size_message' => 'Berkas yang diunggah tidak boleh lebih besar dari :max kilobyte.',

        'tools' => [
            'attach_files' => 'Lampirkan berkas',
            'blockquote' => 'Kutipan',
            'bold' => 'Tebal',
            'bullet_list' => 'Daftar',
            'code_block' => 'Blok kode',
            'heading' => 'Judul',
            'italic' => 'Miring',
            'link' => 'Tautan',
            'ordered_list' => 'Daftar berurut',
            'redo' => 'Kembalikan',
            'strike' => 'Coret',
            'table' => 'Tabel',
            'undo' => 'Batalkan',
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
                'label' => 'Tambahkan ke :label',
            ],

            'add_between' => [
                'label' => 'Sisipkan di antara',
            ],

            'delete' => [
                'label' => 'Hapus',
            ],

            'clone' => [
                'label' => 'Duplikat',
            ],

            'reorder' => [
                'label' => 'Pindahkan',
            ],

            'move_down' => [
                'label' => 'Turunkan',
            ],

            'move_up' => [
                'label' => 'Naikkan',
            ],

            'collapse' => [
                'label' => 'Sembunyikan',
            ],

            'expand' => [
                'label' => 'Tampilkan',
            ],

            'collapse_all' => [
                'label' => 'Sembunyikan semua',
            ],

            'expand_all' => [
                'label' => 'Tampilkan semua',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Unggah berkas',

                'modal' => [

                    'heading' => 'Unggah berkas',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Berkas',
                                'existing' => 'Timpa berkas',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Teks alternatif',
                                'existing' => 'Ganti teks alternatif',
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

            'grid' => [

                'label' => 'Grid',

                'modal' => [

                    'heading' => 'Grid',

                    'form' => [

                        'preset' => [

                            'label' => 'Preset',

                            'placeholder' => 'Tidak ada',

                            'options' => [
                                'two' => 'Dua',
                                'three' => 'Tiga',
                                'four' => 'Empat',
                                'five' => 'Lima',
                                'two_start_third' => 'Dua (Mulai Ketiga)',
                                'two_end_third' => 'Dua (Akhiri Ketiga)',
                                'two_start_fourth' => 'Dua (Mulai Keempat)',
                                'two_end_fourth' => 'Dua (Akhiri Keempat)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Kolom',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Dari breakpoint',

                            'options' => [
                                'default' => 'Semua',
                                'sm' => 'Kecil',
                                'md' => 'Sedang',
                                'lg' => 'Besar',
                                'xl' => 'Ekstra besar',
                                '2xl' => 'Dua ekstra besar',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Dua kolom asimetris',
                        ],

                        'start_span' => [
                            'label' => 'Rentang awal',
                        ],

                        'end_span' => [
                            'label' => 'Rentang akhir',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Ubah',

                'modal' => [

                    'heading' => 'Tautan',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Buka pada tab baru',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Warna teks',

                'modal' => [

                    'heading' => 'Warna teks',

                    'form' => [

                        'color' => [
                            'label' => 'Warna',
                        ],

                        'custom_color' => [
                            'label' => 'Warna khusus',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Berkas yang diunggah harus bertipe: :values.',

        'file_attachments_max_size_message' => 'Berkas yang diunggah tidak boleh lebih besar dari :max kilobyte.',

        'no_merge_tag_search_results_message' => 'Tidak ada tag gabungan yang ditemukan.',

        'tools' => [
            'align_center' => 'Rata tengah',
            'align_end' => 'Rata akhir',
            'align_justify' => 'Rata penuh',
            'align_start' => 'Rata awal',
            'attach_files' => 'Lampirkan berkas',
            'blockquote' => 'Kutipan',
            'bold' => 'Tebal',
            'bullet_list' => 'Daftar',
            'clear_formatting' => 'Hapus format',
            'code' => 'Kode',
            'code_block' => 'Blok kode',
            'custom_blocks' => 'Blok',
            'details' => 'Detail',
            'h1' => 'Judul',
            'h2' => 'Sub judul',
            'h3' => 'Anak judul',
            'grid' => 'Grid',
            'grid_delete' => 'Hapus grid',
            'highlight' => 'Sorot',
            'horizontal_rule' => 'Garis horizontal',
            'italic' => 'Miring',
            'lead' => 'Teks utama',
            'link' => 'Tautan',
            'merge_tags' => 'Tag gabungan',
            'ordered_list' => 'Daftar berurut',
            'redo' => 'Kembalikan',
            'small' => 'Teks kecil',
            'strike' => 'Coret',
            'subscript' => 'Aksara bawah',
            'superscript' => 'Aksara atas',
            'table' => 'Tabel',
            'table_delete' => 'Hapus tabel',
            'table_add_column_before' => 'Tambahkan kolom sebelum',
            'table_add_column_after' => 'Tambahkan kolom sesudah',
            'table_delete_column' => 'Hapus kolom',
            'table_add_row_before' => 'Tambahkan baris diatas',
            'table_add_row_after' => 'Tambahkan baris dibawah',
            'table_delete_row' => 'Hapus baris',
            'table_merge_cells' => 'Gabungkan sel',
            'table_split_cell' => 'Pisahkan sel',
            'table_toggle_header_row' => 'Alihkan Baris Judul',
            'text_color' => 'Warna teks',
            'underline' => 'Garis bawah',
            'undo' => 'Batalkan',
        ],

        'uploading_file_message' => 'Mengunggah berkas...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Buat',

                'modal' => [

                    'heading' => 'Buat',

                    'actions' => [

                        'create' => [
                            'label' => 'Buat',
                        ],

                        'create_another' => [
                            'label' => 'Buat & buat lainnya',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Ubah',

                'modal' => [

                    'heading' => 'Ubah',

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

        'loading_message' => 'Memuat...',

        'max_items_message' => 'Hanya :count yang dapat dipilih.',

        'no_search_results_message' => 'Tidak ada hasil yang sesuai dengan pencarian Anda.',

        'placeholder' => 'Pilih salah satu opsi',

        'searching_message' => 'Sedang mencari...',

        'search_prompt' => 'Ketik untuk mencari...',

    ],

    'tags_input' => [
        'placeholder' => 'Tag baru',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Salin',
                'message' => 'Berhasil disalin',
            ],

            'hide_password' => [
                'label' => 'Sembunyikan kata sandi',
            ],

            'show_password' => [
                'label' => 'Tampilkan kata sandi',
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

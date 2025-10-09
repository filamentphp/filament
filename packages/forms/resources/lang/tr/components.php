<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klonla',
            ],

            'add' => [

                'label' => ':label\'e Ekle',

                'modal' => [

                    'heading' => ':label\'e Ekle',

                    'actions' => [

                        'add' => [
                            'label' => 'Ekle',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Bloklar arasına ekle',

                'modal' => [

                    'heading' => ':label\'e Ekle',

                    'actions' => [

                        'add' => [
                            'label' => 'Ekle',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Sil',
            ],

            'edit' => [

                'label' => 'Düzenle',

                'modal' => [

                    'heading' => 'Bloğu Düzenle',

                    'actions' => [

                        'save' => [
                            'label' => 'Değişiklikleri Kaydet',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Taşı',
            ],

            'move_down' => [
                'label' => 'Aşağı taşı',
            ],

            'move_up' => [
                'label' => 'Yukarı taşı',
            ],

            'collapse' => [
                'label' => 'Daralt',
            ],

            'expand' => [
                'label' => 'Genişlet',
            ],

            'collapse_all' => [
                'label' => 'Tümünü daralt',
            ],

            'expand_all' => [
                'label' => 'Tümünü genişlet',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Tüm seçimi kaldır',
            ],

            'select_all' => [
                'label' => 'Tümünü seç',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'İptal',
                ],

                'drag_crop' => [
                    'label' => 'Sürükleme modu "kırpma"',
                ],

                'drag_move' => [
                    'label' => 'Sürükleme modu "taşıma"',
                ],

                'flip_horizontal' => [
                    'label' => 'Görüntüyü yatay olarak çevir',
                ],

                'flip_vertical' => [
                    'label' => 'Görüntüyü dikey olarak çevir',
                ],

                'move_down' => [
                    'label' => 'Görüntüyü aşağı taşı',
                ],

                'move_left' => [
                    'label' => 'Görüntüyü sola taşı',
                ],

                'move_right' => [
                    'label' => 'Görüntüyü sağa taşı',
                ],

                'move_up' => [
                    'label' => 'Görüntüyü yukarı taşı',
                ],

                'reset' => [
                    'label' => 'Sıfırla',
                ],

                'rotate_left' => [
                    'label' => 'Görüntüyü sola döndür',
                ],

                'rotate_right' => [
                    'label' => 'Görüntüyü sağa döndür',
                ],

                'set_aspect_ratio' => [
                    'label' => 'En boy oranını :ratio olarak ayarla',
                ],

                'save' => [
                    'label' => 'Kaydet',
                ],

                'zoom_100' => [
                    'label' => 'Görüntüyü %100 yakınlaştır',
                ],

                'zoom_in' => [
                    'label' => 'Yakınlaştır',
                ],

                'zoom_out' => [
                    'label' => 'Uzaklaştır',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Yükseklik',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Döndürme',
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

                'label' => 'En boy oranı',

                'no_fixed' => [
                    'label' => 'Serbest',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG dosyalarını düzenleme, ölçeklendirme yaptığınızda kalite kaybına neden olabileceği için tavsiye edilmez.\n Devam etmek istediğinize emin misiniz?',
                    'disabled' => 'SVG dosyalarını düzenleme, ölçeklendirme yaptığınızda kalite kaybına neden olduğu için engellendi.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Satır ekle',
            ],

            'delete' => [
                'label' => 'Satır sil',
            ],

            'reorder' => [
                'label' => 'Satır sırala',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Anahtar',
            ],

            'value' => [
                'label' => 'Değer',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Dosya ekle',
            'blockquote' => 'Alıntı',
            'bold' => 'Kalın',
            'bullet_list' => 'Liste',
            'code_block' => 'Kod bloğu',
            'heading' => 'Başlık',
            'italic' => 'Eğik',
            'link' => 'Bağlantı',
            'ordered_list' => 'Numaralı liste',
            'redo' => 'Yinele',
            'strike' => 'Üstü çizili',
            'table' => 'Tablo',
            'undo' => 'Geri al',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Seç',

                'actions' => [

                    'select' => [
                        'label' => 'Seç',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Evet',
            'false' => 'Hayır',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => ':label\'e ekle',
            ],

            'add_between' => [
                'label' => 'Arasına yerleştir',
            ],

            'delete' => [
                'label' => 'Sil',
            ],

            'clone' => [
                'label' => 'Klonla',
            ],

            'reorder' => [
                'label' => 'Taşı',
            ],

            'move_down' => [
                'label' => 'Aşağı taşı',
            ],

            'move_up' => [
                'label' => 'Yukarı taşı',
            ],

            'collapse' => [
                'label' => 'Daralt',
            ],

            'expand' => [
                'label' => 'Genişlet',
            ],

            'collapse_all' => [
                'label' => 'Tümünü daralt',
            ],

            'expand_all' => [
                'label' => 'Tümünü genişlet',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Dosya yükle',

                'modal' => [

                    'heading' => 'Dosya yükle',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Dosya',
                                'existing' => 'Dosyayı değiştir',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Açıklama metni',
                                'existing' => 'Açıklama metnini değiştir',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Ekle',
                        ],

                        'save' => [
                            'label' => 'Kaydet',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Düzenle',

                'modal' => [

                    'heading' => 'Bağlantı',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Yeni sekmede aç',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Uygun birleşme etiketi bulunamadı.',

        'tools' => [
            'align_center' => 'Ortaya hizala',
            'align_end' => 'Sona hizala',
            'align_justify' => 'İki yana yasla',
            'align_start' => 'Başa hizala',
            'attach_files' => 'Dosya ekle',
            'blockquote' => 'Alıntı',
            'bold' => 'Kalın',
            'bullet_list' => 'Sırasız liste',
            'clear_formatting' => 'Biçimlendirmeyi temizle',
            'code' => 'Kod',
            'code_block' => 'Kod bloğu',
            'custom_blocks' => 'Bloklar',
            'details' => 'Detaylar',
            'h1' => 'Başlık',
            'h2' => 'Başlık 2',
            'h3' => 'Alt başlık',
            'highlight' => 'Vurgula',
            'horizontal_rule' => 'Yatay çizgi',
            'italic' => 'Eğik',
            'lead' => 'Öne çıkan metin',
            'link' => 'Bağlantı',
            'merge_tags' => 'Birleşme etiketleri',
            'ordered_list' => 'Sıralı liste',
            'redo' => 'Yinele',
            'small' => 'Küçük metin',
            'strike' => 'Üstü çizili',
            'subscript' => 'Alt simge',
            'superscript' => 'Üst simge',
            'table' => 'Tablo',
            'table_delete' => 'Tabloyu sil',
            'table_add_column_before' => 'Öncesine sütun ekle',
            'table_add_column_after' => 'Sonrasına sütun ekle',
            'table_delete_column' => 'Sütunu sil',
            'table_add_row_before' => 'Üstüne satır ekle',
            'table_add_row_after' => 'Altına satır ekle',
            'table_delete_row' => 'Satırı sil',
            'table_merge_cells' => 'Hücreleri birleştir',
            'table_split_cell' => 'Hücreyi böl',
            'table_toggle_header_row' => 'Başlık satırını aç/kapat',
            'underline' => 'Altı çizili',
            'undo' => 'Geri al',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Oluştur',

                'modal' => [

                    'heading' => 'Oluştur',

                    'actions' => [

                        'create' => [
                            'label' => 'Oluştur',
                        ],

                        'create_another' => [
                            'label' => 'Oluştur & Yeni oluştur',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Düzenle',

                'modal' => [

                    'heading' => 'Düzenle',

                    'actions' => [

                        'save' => [
                            'label' => 'Kaydet',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Evet',
            'false' => 'Hayır',
        ],

        'loading_message' => 'Yükleniyor...',

        'max_items_message' => 'Sadece :count adet seçilebilir.',

        'no_search_results_message' => 'Arama kriterlerinize uyan seçenek yok.',

        'placeholder' => 'Bir seçenek seçin',

        'searching_message' => 'Aranıyor...',

        'search_prompt' => 'Aramak için yazmaya başlayın...',

    ],

    'tags_input' => [
        'placeholder' => 'Yeni etiket',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Kopyala',
                'message' => 'Kopyalandı',
            ],

            'hide_password' => [
                'label' => 'Şifreyi gizle',
            ],

            'show_password' => [
                'label' => 'Şifreyi göster',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Evet',
            'false' => 'Hayır',
        ],

    ],

];

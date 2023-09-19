<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klonla',
            ],

            'add' => [
                'label' => ':label ekle',
            ],

            'add_between' => [
                'label' => 'Bloklar arasına ekle',
            ],

            'delete' => [
                'label' => 'Sil',
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
                'label' => 'Küçült',
            ],

            'expand' => [
                'label' => 'Genişlet',
            ],

            'collapse_all' => [
                'label' => 'Tümünü küçült',
            ],

            'expand_all' => [
                'label' => 'Tümünü genişlet',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Tümünü seçmeyi kaldır',
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
                    'label' => 'Kırpma moduna sürükleyin',
                ],

                'drag_move' => [
                    'label' => 'Taşıma moduna sürükleyin',
                ],

                'flip_horizontal' => [
                    'label' => 'Görüntüyü yatay çevir',
                ],

                'flip_vertical' => [
                    'label' => 'Görüntüyü dikey çevir',
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

                'label' => 'En boy oranları',

                'no_fixed' => [
                    'label' => 'Serbest',
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

        'toolbar_buttons' => [
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

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => ':label\'e ekle',
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
                'label' => 'Küçült',
            ],

            'expand' => [
                'label' => 'Genişlet',
            ],

            'collapse_all' => [
                'label' => 'Tümünü küçült',
            ],

            'expand_all' => [
                'label' => 'Tümünü genişlet',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Bağlantı',
                    'unlink' => 'Bağlantıyı kaldır',
                ],

                'label' => 'URL',

                'placeholder' => 'Bir URL girin',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Dosya ekle',
            'blockquote' => 'Alıntı',
            'bold' => 'Kalın',
            'bullet_list' => 'Sırasız liste',
            'code_block' => 'Kod bloğu',
            'h1' => 'Başlık',
            'h2' => 'Başlık 2',
            'h3' => 'Alt başlık',
            'italic' => 'Eğik',
            'link' => 'Bağlantı',
            'ordered_list' => 'Sıralı liste',
            'redo' => 'Yinele',
            'strike' => 'Üstü çizili',
            'underline' => 'Altı çizili',
            'undo' => 'Geri al',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Oluştur',

                    'actions' => [

                        'create' => [
                            'label' => 'Oluştur',
                        ],

                        'create_another' => [
                            'label' => 'Oluştur & başka bir tane oluştur',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

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

        'max_items_message' => 'Sadece :count seçilebilir.',

        'no_search_results_message' => 'Arama kriterlerinize uyan seçenek yok.',

        'placeholder' => 'Bir seçenek seçin',

        'searching_message' => 'Aranıyor...',

        'search_prompt' => 'Aramak için yazmaya başlayın...',

    ],

    'tags_input' => [
        'placeholder' => 'Yeni etiket',
    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Geri',
            ],

            'next_step' => [
                'label' => 'İleri',
            ],

        ],

    ],

];

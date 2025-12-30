<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => '複製',
            ],

            'add' => [

                'label' => ':labelを追加',

                'modal' => [

                    'heading' => ':labelを追加',

                    'actions' => [

                        'add' => [
                            'label' => '追加',
                        ],

                    ],

                ],
            ],

            'add_between' => [

                'label' => 'ブロックの間に追加',

                'modal' => [

                    'heading' => ':labelを追加',

                    'actions' => [

                        'add' => [
                            'label' => '追加',
                        ],

                    ],

                ],

            ],

            'delete' => [

                'label' => '削除',
            ],

            'edit' => [

                'label' => '編集',

                'modal' => [

                    'heading' => 'ブロックを編集',

                    'actions' => [

                        'save' => [
                            'label' => '変更を保存',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => '移動',
            ],

            'move_down' => [
                'label' => '下に移動',
            ],

            'move_up' => [
                'label' => '上に移動',
            ],

            'collapse' => [
                'label' => '折り畳む',
            ],

            'expand' => [
                'label' => '展開',
            ],

            'collapse_all' => [
                'label' => 'すべて折り畳む',
            ],

            'expand_all' => [
                'label' => 'すべて展開',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'すべて解除',
            ],

            'select_all' => [
                'label' => 'すべて選択',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'キャンセル',
                ],

                'drag_crop' => [
                    'label' => 'ドラッグモード "クロップ"',
                ],

                'drag_move' => [
                    'label' => 'ドラッグモード "移動"',
                ],

                'flip_horizontal' => [
                    'label' => '水平フリップ',
                ],

                'flip_vertical' => [
                    'label' => '垂直フリップ',
                ],

                'move_down' => [
                    'label' => '下に移動',
                ],

                'move_left' => [
                    'label' => '左に移動',
                ],

                'move_right' => [
                    'label' => '右に移動',
                ],

                'move_up' => [
                    'label' => '上に移動',
                ],

                'reset' => [
                    'label' => 'リセット',
                ],

                'rotate_left' => [
                    'label' => '左回転',
                ],

                'rotate_right' => [
                    'label' => '右回転',
                ],

                'set_aspect_ratio' => [
                    'label' => 'アスペクト比を:ratioにセット',
                ],

                'save' => [
                    'label' => '保存',
                ],

                'zoom_100' => [
                    'label' => '100%にズーム',
                ],

                'zoom_in' => [
                    'label' => 'ズームイン',
                ],

                'zoom_out' => [
                    'label' => 'ズームアウト',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => '高さ',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => '回転',
                    'unit' => '度',
                ],

                'width' => [
                    'label' => '幅',
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

                'label' => 'アスペクト比',

                'no_fixed' => [
                    'label' => 'フリー',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVGファイルの編集は拡大縮小する際に品質の低下を引き起こす可能性があるため、お勧めしません。\n 続行しますか？',
                    'disabled' => '拡大縮小する際に品質が低下する可能性があるためSVGファイルの編集は無効になっています',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => '行を追加',
            ],

            'delete' => [
                'label' => '行を削除',
            ],

            'reorder' => [
                'label' => '行の並べ替え',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'キー',
            ],

            'value' => [
                'label' => '値',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'アップロードできるファイルの種類は :values です。',

        'file_attachments_max_size_message' => 'アップロードできるファイルの最大サイズは :max キロバイトです。',

        'tools' => [
            'attach_files' => 'ファイルを添付',
            'blockquote' => 'ブロック引用',
            'bold' => '太字',
            'bullet_list' => '箇条書き',
            'code_block' => 'コードブロック',
            'heading' => '見出し',
            'italic' => 'イタリック',
            'link' => 'リンク',
            'ordered_list' => '番号付きリスト',
            'redo' => 'やり直し',
            'strike' => '打ち消し線',
            'table' => '表',
            'undo' => '元に戻す',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => '選択',

                'actions' => [

                    'select' => [
                        'label' => '選択',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'はい',
            'false' => 'いいえ',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => ':labelへ追加',
            ],

            'add_between' => [
                'label' => '間に挿入',
            ],

            'delete' => [
                'label' => '削除',
            ],

            'clone' => [
                'label' => '複製',
            ],

            'reorder' => [
                'label' => '移動',
            ],

            'move_down' => [
                'label' => '下に移動',
            ],

            'move_up' => [
                'label' => '上に移動',
            ],

            'collapse' => [
                'label' => '折り畳む',
            ],

            'expand' => [
                'label' => '展開',
            ],

            'collapse_all' => [
                'label' => 'すべて折り畳む',
            ],

            'expand_all' => [
                'label' => 'すべて展開',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'ファイルをアップロード',

                'modal' => [

                    'heading' => 'ファイルをアップロード',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'ファイル',
                                'existing' => 'ファイルを置き換える',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => '代替テキスト',
                                'existing' => '代替テキストを変更',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => '挿入',
                        ],

                        'save' => [
                            'label' => '保存',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'グリッド',

                'modal' => [

                    'heading' => 'グリッド設定',

                    'form' => [

                        'preset' => [

                            'label' => 'プリセット',

                            'placeholder' => 'なし',

                            'options' => [
                                'two' => '2列',
                                'three' => '3列',
                                'four' => '4列',
                                'five' => '5列',
                                'two_start_third' => '2列（開始位置：3分の1）',
                                'two_end_third' => '2列（終了位置：3分の1）',
                                'two_start_fourth' => '2列（開始位置：4分の1）',
                                'two_end_fourth' => '2列（終了位置：4分の1）',
                            ],
                        ],

                        'columns' => [
                            'label' => '列数',
                        ],

                        'from_breakpoint' => [

                            'label' => 'ブレークポイント',

                            'options' => [
                                'default' => 'すべて',
                                'sm' => '小',
                                'md' => '中',
                                'lg' => '大',
                                'xl' => '特大',
                                '2xl' => '超特大',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => '非対称の2列',
                        ],

                        'start_span' => [
                            'label' => '開始スパン',
                        ],

                        'end_span' => [
                            'label' => '終了スパン',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'リンク',

                'modal' => [

                    'heading' => 'リンク設定',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => '新しいタブで開く',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => '文字色',

                'modal' => [

                    'heading' => '文字色',

                    'form' => [

                        'color' => [
                            'label' => '色',
                        ],

                        'custom_color' => [
                            'label' => 'カスタムカラー',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'アップロードできるファイルの種類は :values です。',

        'file_attachments_max_size_message' => 'アップロードできるファイルの最大サイズは :max キロバイトです。',

        'no_merge_tag_search_results_message' => '一致するマージタグがありません。',

        'tools' => [
            'align_center' => '中央揃え',
            'align_end' => '右揃え',
            'align_justify' => '両端揃え',
            'align_start' => '左揃え',
            'attach_files' => 'ファイルを添付',
            'blockquote' => '引用',
            'bold' => '太字',
            'bullet_list' => '箇条書きリスト',
            'clear_formatting' => '書式をクリア',
            'code' => 'コード',
            'code_block' => 'コードブロック',
            'custom_blocks' => 'ブロック',
            'details' => '詳細',
            'h1' => 'タイトル',
            'h2' => '見出し',
            'h3' => '小見出し',
            'grid' => 'グリッド',
            'grid_delete' => 'グリッドを削除',
            'highlight' => 'ハイライト',
            'horizontal_rule' => '区切り線',
            'italic' => '斜体',
            'lead' => 'リード文',
            'link' => 'リンク',
            'merge_tags' => 'マージタグ',
            'ordered_list' => '番号付きリスト',
            'redo' => 'やり直し',
            'small' => '小文字',
            'strike' => '取り消し線',
            'subscript' => '下付き文字',
            'superscript' => '上付き文字',
            'table' => '表',
            'table_delete' => '表を削除',
            'table_add_column_before' => '左に列を追加',
            'table_add_column_after' => '右に列を追加',
            'table_delete_column' => '列を削除',
            'table_add_row_before' => '上に行を追加',
            'table_add_row_after' => '下に行を追加',
            'table_delete_row' => '行を削除',
            'table_merge_cells' => 'セルを結合',
            'table_split_cell' => 'セルを分割',
            'table_toggle_header_row' => 'ヘッダー行を切り替え',
            'text_color' => '文字色',
            'underline' => '下線',
            'undo' => '元に戻す',
        ],

        'uploading_file_message' => 'ファイルをアップロードしています...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => '作成',

                'modal' => [

                    'heading' => '作成',

                    'actions' => [

                        'create' => [
                            'label' => '作成',
                        ],

                        'create_another' => [
                            'label' => '保存して、続けて作成',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => '編集',

                'modal' => [

                    'heading' => '編集',

                    'actions' => [

                        'save' => [
                            'label' => '保存',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'はい',
            'false' => 'いいえ',
        ],

        'loading_message' => '読み込み中...',

        'max_items_message' => ':count個のみ選択されてます',

        'no_search_results_message' => '検索結果はありませんでした',

        'placeholder' => 'オプションを選択',

        'searching_message' => '検索中...',

        'search_prompt' => '検索キーワードを入力...',

    ],

    'tags_input' => [
        'placeholder' => '新規タグ',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'コピー',
                'message' => 'コピーしました',
            ],

            'hide_password' => [
                'label' => 'パスワードを非表示',
            ],

            'show_password' => [
                'label' => 'パスワードを表示',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'はい',
            'false' => 'いいえ',
        ],

    ],

];

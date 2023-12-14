<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => '複製',
            ],

            'add' => [
                'label' => ':labelを追加',
            ],

            'add_between' => [
                'label' => 'ブロックの間に追加',
            ],

            'delete' => [
                'label' => '削除',
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

        'toolbar_buttons' => [
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

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'リンク追加',
                    'unlink' => 'リンク解除',
                ],

                'label' => 'URL',

                'placeholder' => 'URLを入力',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'ファイルを添付',
            'blockquote' => 'ブロック引用',
            'bold' => '太字',
            'bullet_list' => '箇条書き',
            'code_block' => 'コードブロック',
            'h1' => 'タイトル',
            'h2' => '見出し',
            'h3' => '小見出し',
            'italic' => 'イタリック',
            'link' => 'リンク',
            'ordered_list' => '番号付きリスト',
            'redo' => 'やり直し',
            'strike' => '打ち消し線',
            'underline' => '下線',
            'undo' => '元に戻す',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

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

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => '前へ',
            ],

            'next_step' => [
                'label' => '次へ',
            ],

        ],

    ],

];

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
                'label' => '全て折り畳む',
            ],

            'expand_all' => [
                'label' => '全て展開',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Deselect all',
            ],

            'select_all' => [
                'label' => 'Select all',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Cancel',
                ],

                'drag_crop' => [
                    'label' => 'Drag mode "crop"',
                ],

                'drag_move' => [
                    'label' => 'Drag mode "move"',
                ],

                'flip_horizontal' => [
                    'label' => 'Flip image horizontally',
                ],

                'flip_vertical' => [
                    'label' => 'Flip image vertically',
                ],

                'move_down' => [
                    'label' => 'Move image down',
                ],

                'move_left' => [
                    'label' => 'Move image to left',
                ],

                'move_right' => [
                    'label' => 'Move image to right',
                ],

                'move_up' => [
                    'label' => 'Move image up',
                ],

                'reset' => [
                    'label' => 'Reset',
                ],

                'rotate_left' => [
                    'label' => 'Rotate image to left',
                ],

                'rotate_right' => [
                    'label' => 'Rotate image to right',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Set aspect ratio to :ratio',
                ],

                'save' => [
                    'label' => 'Save',
                ],

                'zoom_100' => [
                    'label' => 'Zoom image to 100%',
                ],

                'zoom_in' => [
                    'label' => 'Zoom in',
                ],

                'zoom_out' => [
                    'label' => 'Zoom out',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Height',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotation',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Width',
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

                'label' => 'Aspect ratios',

                'no_fixed' => [
                    'label' => 'Free',
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
                'label' => 'Reorder row',
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
            'attach_files' => 'ファイルを追加',
            'blockquote' => 'ブロック引用',
            'bold' => '太文字',
            'bullet_list' => '箇条書き',
            'code_block' => 'コードブロック',
            'heading' => 'Heading',
            'italic' => 'イタリック体',
            'link' => 'リンク',
            'ordered_list' => '番号付きリスト',
            'redo' => 'Redo',
            'strike' => 'Strikethrough',
            'table' => 'Table',
            'undo' => 'Undo',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => ':labelへ追加',
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
                'label' => '全て折り畳む',
            ],

            'expand_all' => [
                'label' => '全て展開',
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
            'bold' => '太文字',
            'bullet_list' => '箇条書き',
            'code_block' => 'コードブロック',
            'h1' => '見出し1(h1)',
            'h2' => '見出し2(h2)',
            'h3' => '見出し3(h3)',
            'italic' => 'イタリック体',
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
                            'label' => 'Create & create another',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Edit',

                    'actions' => [

                        'save' => [
                            'label' => 'Save',
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

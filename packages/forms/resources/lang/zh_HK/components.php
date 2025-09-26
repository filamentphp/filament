<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => '克隆',
            ],

            'add' => [

                'label' => '加至 :label',

                'modal' => [

                    'heading' => '加至 :label',

                    'actions' => [

                        'add' => [
                            'label' => '加至',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => '於區塊之間插入',

                'modal' => [

                    'heading' => '加至 :label',

                    'actions' => [

                        'add' => [
                            'label' => '加至',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => '刪除',
            ],

            'edit' => [

                'label' => '編輯',

                'modal' => [

                    'heading' => '編輯塊',

                    'actions' => [

                        'save' => [
                            'label' => '儲存變更',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => '重新排序',
            ],

            'move_down' => [
                'label' => '下移',
            ],

            'move_up' => [
                'label' => '上移',
            ],

            'collapse' => [
                'label' => '收起',
            ],

            'expand' => [
                'label' => '展開項目',
            ],

            'collapse_all' => [
                'label' => '收起全部',
            ],

            'expand_all' => [
                'label' => '展開全部',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => '取消全選',
            ],

            'select_all' => [
                'label' => '全選',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => '取消',
                ],

                'drag_crop' => [
                    'label' => '拖動模式「裁剪」',
                ],

                'drag_move' => [
                    'label' => '拖動模式「移動」',
                ],

                'flip_horizontal' => [
                    'label' => '水平翻轉圖像',
                ],

                'flip_vertical' => [
                    'label' => '垂直翻轉圖像',
                ],

                'move_down' => [
                    'label' => '向下移動圖像',
                ],

                'move_left' => [
                    'label' => '將圖像移至左側',
                ],

                'move_right' => [
                    'label' => '將圖像移至右側',
                ],

                'move_up' => [
                    'label' => '向上移動圖像',
                ],

                'reset' => [
                    'label' => '重置',
                ],

                'rotate_left' => [
                    'label' => '將圖像向左旋轉',
                ],

                'rotate_right' => [
                    'label' => '將圖像向右旋轉',
                ],

                'set_aspect_ratio' => [
                    'label' => '將長寬比設定為 :ratio',
                ],

                'save' => [
                    'label' => '儲存',
                ],

                'zoom_100' => [
                    'label' => '將圖像縮放到100%',
                ],

                'zoom_in' => [
                    'label' => '放大',
                ],

                'zoom_out' => [
                    'label' => '縮小',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => '高度',
                    'unit' => '像素',
                ],

                'rotation' => [
                    'label' => '旋轉',
                    'unit' => '度',
                ],

                'width' => [
                    'label' => '寬度',
                    'unit' => '像素',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => '像素',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => '像素',
                ],

            ],

            'aspect_ratios' => [

                'label' => '長寬比',

                'no_fixed' => [
                    'label' => '自由',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => "不建議編輯 SVG 檔案，因為在縮放時可能會導致畫質損失。\n確定要繼續嗎？",
                    'disabled' => '已停用 SVG 編輯，因為在縮放時可能會導致畫質損失。',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => '新增橫列',
            ],

            'delete' => [
                'label' => '刪除橫列',
            ],

            'reorder' => [
                'label' => '重新排序行',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => '索引鍵',
            ],

            'value' => [
                'label' => '值',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => '上載的檔案必須為以下類型：:values。',

        'file_attachments_max_size_message' => '上載的檔案大小不得超過 :max 千字節。',

        'tools' => [
            'attach_files' => '附加檔案',
            'blockquote' => '引用',
            'bold' => '粗體',
            'bullet_list' => '項目符號清單',
            'code_block' => '程式碼',
            'heading' => '標題',
            'italic' => '斜體',
            'link' => '連結',
            'ordered_list' => '數字列表',
            'redo' => '重做',
            'strike' => '刪除線',
            'table' => '表格',
            'undo' => '復原',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => '選擇',

                'actions' => [

                    'select' => [
                        'label' => '選擇',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => '加至 :label',
            ],

            'add_between' => [
                'label' => '於區塊之間插入',
            ],

            'delete' => [
                'label' => '刪除',
            ],

            'clone' => [
                'label' => '克隆',
            ],

            'reorder' => [
                'label' => '重新排序',
            ],

            'move_down' => [
                'label' => '下移',
            ],

            'move_up' => [
                'label' => '上移',
            ],

            'collapse' => [
                'label' => '收起',
            ],

            'expand' => [
                'label' => '展開',
            ],

            'collapse_all' => [
                'label' => '收起全部',
            ],

            'expand_all' => [
                'label' => '展開全部',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => '上載檔案',

                'modal' => [

                    'heading' => '上載檔案',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => '檔案',
                                'existing' => '替換檔案',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alt 文字',
                                'existing' => '替換 alt 文字',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => '插入',
                        ],

                        'save' => [
                            'label' => '儲存',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => '網格',

                'modal' => [

                    'heading' => '網格',

                    'form' => [

                        'preset' => [

                            'label' => '預設',

                            'placeholder' => '無',

                            'options' => [
                                'two' => '兩列',
                                'three' => '三列',
                                'four' => '四列',
                                'five' => '五列',
                                'two_start_third' => '兩列（首列三分之一）',
                                'two_end_third' => '兩列（末列三分之一）',
                                'two_start_fourth' => '兩列（首列四分之一）',
                                'two_end_fourth' => '兩列（末列四分之一）',
                            ],
                        ],

                        'columns' => [
                            'label' => '列數',
                        ],

                        'from_breakpoint' => [

                            'label' => '從斷點開始',

                            'options' => [
                                'default' => '全部',
                                'sm' => '細螢幕',
                                'md' => '中螢幕',
                                'lg' => '大螢幕',
                                'xl' => '超大螢幕',
                                '2xl' => '超超大螢幕',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => '非對稱兩列',
                        ],

                        'start_span' => [
                            'label' => '開始跨度',
                        ],

                        'end_span' => [
                            'label' => '結束跨度',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => '連結',

                'modal' => [

                    'heading' => '連結',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => '在新分頁中開啟',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => '文字顏色',

                'modal' => [

                    'heading' => '文字顏色',

                    'form' => [

                        'color' => [
                            'label' => '顏色',
                        ],

                        'custom_color' => [
                            'label' => '自訂顏色',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => '上載的檔案必須為以下類型：:values。',

        'file_attachments_max_size_message' => '上載的檔案大小不得超過 :max 千字節。',

        'no_merge_tag_search_results_message' => '未找到合併標籤結果。',

        'tools' => [
            'align_center' => '置中對齊',
            'align_end' => '靠右對齊',
            'align_justify' => '兩端對齊',
            'align_start' => '靠左對齊',
            'attach_files' => '附加檔案',
            'blockquote' => '引用',
            'bold' => '粗體',
            'bullet_list' => '項目符號清單',
            'clear_formatting' => '清除格式',
            'code' => '程式碼',
            'code_block' => '程式碼區塊',
            'custom_blocks' => '自訂區塊',
            'details' => '詳情',
            'h1' => '標題',
            'h2' => '副標題',
            'h3' => '小標題',
            'grid' => '網格',
            'grid_delete' => '刪除網格',
            'highlight' => '高亮',
            'horizontal_rule' => '水平線',
            'italic' => '斜體',
            'lead' => '導語',
            'link' => '連結',
            'merge_tags' => '合併標籤',
            'ordered_list' => '數字列表',
            'redo' => '重做',
            'small' => '小字體',
            'strike' => '刪除線',
            'subscript' => '下標',
            'superscript' => '上標',
            'table' => '表格',
            'table_delete' => '刪除表格',
            'table_add_column_before' => '在前插入欄',
            'table_add_column_after' => '在後插入欄',
            'table_delete_column' => '刪除欄',
            'table_add_row_before' => '在上方插入列',
            'table_add_row_after' => '在下方插入列',
            'table_delete_row' => '刪除列',
            'table_merge_cells' => '合併儲存格',
            'table_split_cell' => '拆分儲存格',
            'table_toggle_header_row' => '切換表頭列',
            'text_color' => '文字顏色',
            'underline' => '底線',
            'undo' => '復原',
        ],

        'uploading_file_message' => '正在上載檔案...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => '建立',

                'modal' => [

                    'heading' => '建立',

                    'actions' => [

                        'create' => [
                            'label' => '建立',
                        ],

                        'create_another' => [
                            'label' => '建立並繼續建立',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => '編輯',

                'modal' => [

                    'heading' => '編輯',

                    'actions' => [

                        'save' => [
                            'label' => '儲存',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

        'loading_message' => '載入中...',

        'max_items_message' => '只能選擇 :count 個。',

        'no_search_results_message' => '沒有選項符合您的搜尋',

        'placeholder' => '選擇選項',

        'searching_message' => '搜尋中...',

        'search_prompt' => '輸入內容以搜尋...',

    ],

    'tags_input' => [
        'placeholder' => '新增標籤',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => '複製',
                'message' => '已複製',
            ],

            'hide_password' => [
                'label' => '隱藏密碼',
            ],

            'show_password' => [
                'label' => '顯示密碼',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

    ],

];

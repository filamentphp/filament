<?php

return [

    'builder' => [

        'reordering' => [
            'item' => '區塊',
            'position' => ':label，第 :position 個，共 :count 個',
            'moved' => ':label 已移至第 :position 個，共 :count 個',
        ],

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

                'label' => '在區塊之間插入',

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
                'label' => '移動',
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
                'label' => '全部收起',
            ],

            'expand_all' => [
                'label' => '全部展開',
            ],

        ],

        'block_picker' => [

            'no_search_results_message' => '沒有符合搜尋的區塊。',

            'search_prompt' => '搜尋區塊',

        ],

    ],

    'checkbox_list' => [

        'required_description' => '請至少選擇一個選項。',

        'actions' => [

            'deselect_all' => [
                'label' => '取消全選',
            ],

            'select_all' => [
                'label' => '全選',
            ],

        ],

    ],

    'color_picker' => [

        'panel_label' => '顏色選擇器',

    ],

    'date_time_picker' => [

        'month_select' => [
            'label' => '月份',
        ],

        'year_input' => [
            'label' => '年份',
        ],

        'hour_input' => [
            'label' => '小時',
        ],

        'minute_input' => [
            'label' => '分鐘',
        ],

        'second_input' => [
            'label' => '秒',
        ],

    ],

    'file_upload' => [

        'placeholder' => '尚未上傳檔案',

        'actions' => [

            'download' => [
                'label' => '下載',
            ],

            'open' => [
                'label' => '在新分頁中開啟',
            ],

        ],

        'editor' => [

            'label' => '影像編輯器',

            'actions' => [

                'cancel' => [
                    'label' => '取消',
                ],

                'drag_crop' => [
                    'label' => '拖曳模式 "裁切"',
                ],

                'drag_move' => [
                    'label' => '拖曳模式 "移動"',
                ],

                'flip_horizontal' => [
                    'label' => '水平翻轉影像',
                ],

                'flip_vertical' => [
                    'label' => '垂直翻轉影像',
                ],

                'move_down' => [
                    'label' => '向下移動影像',
                ],

                'move_left' => [
                    'label' => '將影像移至左側',
                ],

                'move_right' => [
                    'label' => '將影像移至右側',
                ],

                'move_up' => [
                    'label' => '向上移動影像',
                ],

                'reset' => [
                    'label' => '重置',
                ],

                'rotate_left' => [
                    'label' => '將影像向左旋轉',
                ],

                'rotate_right' => [
                    'label' => '將影像向右旋轉',
                ],

                'set_aspect_ratio' => [
                    'label' => '將縱橫比設定為 :ratio',
                ],

                'save' => [
                    'label' => '儲存',
                ],

                'zoom_100' => [
                    'label' => '將影像縮放到100%',
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

                'label' => '縱橫比',

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

            'move_down' => [
                'label' => '下移',
            ],

            'move_up' => [
                'label' => '上移',
            ],

            'reorder' => [
                'label' => '重新排序行',
            ],

        ],

        'columns' => [

            'actions' => [
                'label' => '操作',
            ],

            'reorder' => [
                'label' => '重新排序',
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

        'reordering' => [
            'row' => '橫列',
            'position' => ':label，第 :position 列，共 :count 列',
            'moved' => ':label 已移至第 :position 列，共 :count 列',
        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => '上傳的檔案必須為以下類型：:values。',

        'file_attachments_max_size_message' => '上傳的檔案大小不得超過 :max 千字節。',

        'tools' => [
            'attach_files' => '附件',
            'blockquote' => '引用',
            'bold' => '加粗',
            'bullet_list' => '普通列表',
            'code_block' => '程式碼',
            'heading' => '標題',
            'italic' => '斜體',
            'link' => '連結',
            'ordered_list' => '數字列表',
            'redo' => '重做',
            'strike' => '刪除線',
            'table' => '表格',
            'undo' => '撤銷',
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

        'reordering' => [
            'item' => '項目',
            'position' => ':label，第 :position 項，共 :count 項',
            'moved' => ':label 已移至第 :position 項，共 :count 項',
        ],

        'columns' => [

            'actions' => [
                'label' => '操作',
            ],

            'reorder' => [
                'label' => '重新排序',
            ],

        ],

        'actions' => [

            'add' => [
                'label' => '加至 :label',
            ],

            'add_between' => [
                'label' => '在區塊之間插入',
            ],

            'delete' => [
                'label' => '刪除',
            ],

            'clone' => [
                'label' => '克隆',
            ],

            'reorder' => [
                'label' => '移動',
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
                'label' => '全部收起',
            ],

            'expand_all' => [
                'label' => '全部展開',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => '上傳檔案',

                'modal' => [

                    'heading' => '上傳檔案',

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

            'close_panel' => [
                'label' => '關閉面板',
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
                                'sm' => '小螢幕',
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

                            'options' => [
                                'slate' => '藍灰色',
                                'gray' => '灰色',
                                'zinc' => '冷灰色',
                                'neutral' => '中性灰',
                                'stone' => '暖灰色',
                                'mauve' => '灰紫色',
                                'olive' => '橄欖綠',
                                'mist' => '霧灰色',
                                'taupe' => '灰褐色',
                                'red' => '紅色',
                                'orange' => '橘色',
                                'amber' => '琥珀色',
                                'yellow' => '黃色',
                                'lime' => '萊姆綠',
                                'green' => '綠色',
                                'emerald' => '翠綠色',
                                'teal' => '藍綠色',
                                'cyan' => '青色',
                                'sky' => '天藍色',
                                'blue' => '藍色',
                                'indigo' => '靛藍色',
                                'violet' => '紫羅蘭色',
                                'purple' => '紫色',
                                'fuchsia' => '洋紅色',
                                'pink' => '粉紅色',
                                'rose' => '玫瑰紅',
                            ],
                        ],

                        'custom_color' => [
                            'label' => '自訂顏色',
                        ],

                    ],

                ],

            ],

        ],

        'custom_blocks' => [

            'actions' => [

                'delete' => [
                    'label' => '刪除區塊',
                ],

                'edit' => [
                    'label' => '編輯區塊',
                ],

            ],

            'no_search_results_message' => '沒有符合搜尋的區塊。',

            'search_label' => '搜尋區塊',

            'search_prompt' => '搜尋區塊',

        ],

        'file_attachments_accepted_file_types_message' => '上傳的檔案必須為以下類型：:values。',

        'file_attachments_max_size_message' => '上傳的檔案大小不得超過 :max 千字節。',

        'no_merge_tag_search_results_message' => '未找到合併標籤結果。',

        'mentions' => [
            'no_options_message' => '沒有可用的選項。',
            'no_search_results_message' => '沒有符合搜尋的結果。',
            'search_prompt' => '輸入內容以搜尋...',
            'searching_message' => '搜尋中...',
        ],

        'toolbar' => [
            'label' => '編輯器工具列',
        ],

        'tools' => [
            'align_center' => '置中對齊',
            'align_end' => '靠右對齊',
            'align_justify' => '兩端對齊',
            'align_start' => '靠左對齊',
            'attach_files' => '附件',
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
            'h4' => '四級標題',
            'h5' => '五級標題',
            'h6' => '六級標題',
            'grid' => '網格',
            'grid_delete' => '刪除網格',
            'highlight' => '高亮',
            'horizontal_rule' => '水平線',
            'italic' => '斜體',
            'lead' => '導語',
            'link' => '連結',
            'merge_tags' => '合併標籤',
            'ordered_list' => '數字列表',
            'paragraph' => '段落',
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
            'table_toggle_header_cell' => '切換表頭儲存格',
            'text_color' => '文字顏色',
            'underline' => '底線',
            'undo' => '復原',
        ],

        'uploading_file_message' => '正在上傳檔案...',

    ],

    'select' => [

        'actions' => [

            'clear' => [
                'label' => '清除選擇',
            ],

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

            'remove_option' => [
                'label' => '移除 :label',
            ],

        ],

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

        'error_message' => '無法載入選項，請再試一次。',

        'loading_message' => '載入中...',

        'max_items_message' => '只能選擇 :count 個。',

        'no_options_message' => '沒有可用的選項。',

        'no_search_results_message' => '沒有選項符合您的搜尋',

        'placeholder' => '選擇選項',

        'required_description' => '請選擇一個選項。',

        'searching_message' => '搜尋中...',

        'search_label' => '搜尋',

        'search_prompt' => '輸入內容以搜尋...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => '刪除',
            ],

        ],

        'placeholder' => '新增標籤',

        'tag_added' => '已新增：:tag',

        'tag_removed' => '已移除：:tag',

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

        'required_description' => '請至少選擇一個選項。',

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

    ],

];

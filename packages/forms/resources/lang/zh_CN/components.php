<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => '克隆',
            ],

            'add' => [
                'label' => '添加 :label',
            ],

            'add_between' => [
                'label' => '在块之间插入',
            ],

            'delete' => [
                'label' => '删除',
            ],

            'reorder' => [
                'label' => '移动',
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
                'label' => '展开',
            ],

            'collapse_all' => [
                'label' => '全部收起',
            ],

            'expand_all' => [
                'label' => '全部展开',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => '取消全选',
            ],

            'select_all' => [
                'label' => '全选',
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
                    'label' => '拖动模式“裁剪”',
                ],

                'drag_move' => [
                    'label' => '拖动模式“移动”',
                ],

                'flip_horizontal' => [
                    'label' => '水平翻转图像',
                ],

                'flip_vertical' => [
                    'label' => '垂直翻转图像',
                ],

                'move_down' => [
                    'label' => '向下移动图像',
                ],

                'move_left' => [
                    'label' => '将图像移动到左侧',
                ],

                'move_right' => [
                    'label' => '将图像移动到右侧',
                ],

                'move_up' => [
                    'label' => '向上移动图像',
                ],

                'reset' => [
                    'label' => '重置',
                ],

                'rotate_left' => [
                    'label' => '将图像向左旋转',
                ],

                'rotate_right' => [
                    'label' => '将图像向右旋转',
                ],

                'set_aspect_ratio' => [
                    'label' => '将纵横比设置为 :ratio',
                ],

                'save' => [
                    'label' => '保存',
                ],

                'zoom_100' => [
                    'label' => '将图像缩放到100%',
                ],

                'zoom_in' => [
                    'label' => '放大',
                ],

                'zoom_out' => [
                    'label' => '缩小',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => '高度',
                    'unit' => '像素',
                ],

                'rotation' => [
                    'label' => '旋转',
                    'unit' => '度',
                ],

                'width' => [
                    'label' => '宽度',
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

                'label' => '纵横比',

                'no_fixed' => [
                    'label' => '自由',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => '添加行',
            ],

            'delete' => [
                'label' => '删除行',
            ],

            'reorder' => [
                'label' => '重新排序行',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => '键名',
            ],

            'value' => [
                'label' => '值',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => '附件',
            'blockquote' => '引用',
            'bold' => '加粗',
            'bullet_list' => '普通列表',
            'code_block' => '代码',
            'heading' => '标题',
            'italic' => '斜体',
            'link' => '链接',
            'ordered_list' => '数字列表',
            'redo' => '重做',
            'strike' => '删除线',
            'table' => '表格',
            'undo' => '撤销',
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
                'label' => '添加 :label',
            ],

            'delete' => [
                'label' => '删除',
            ],

            'clone' => [
                'label' => '克隆',
            ],

            'reorder' => [
                'label' => '移动',
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
                'label' => '展开',
            ],

            'collapse_all' => [
                'label' => '全部收起',
            ],

            'expand_all' => [
                'label' => '全部展开',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => '链接',
                    'unlink' => '取消链接',
                ],

                'label' => 'URL',

                'placeholder' => '输入URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => '附件',
            'blockquote' => '引用',
            'bold' => '加粗',
            'bullet_list' => '普通列表',
            'code_block' => '代码',
            'h1' => '标题',
            'h2' => '标题',
            'h3' => '副标题',
            'italic' => '斜体',
            'link' => '链接',
            'ordered_list' => '数字列表',
            'redo' => '重做',
            'strike' => '删除线',
            'underline' => '下划线',
            'undo' => '撤销',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => '创建',

                    'actions' => [

                        'create' => [
                            'label' => '创建',
                        ],

                        'create_another' => [
                            'label' => '创建并继续创建',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => '编辑',

                    'actions' => [

                        'save' => [
                            'label' => '保存',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

        'loading_message' => '加载中...',

        'max_items_message' => '只能选择 :count 个。',

        'no_search_results_message' => '没有选项匹配您的搜索',

        'placeholder' => '选择选项',

        'searching_message' => '搜索中...',

        'search_prompt' => '输入内容以搜索...',

    ],

    'tags_input' => [
        'placeholder' => '新标签',
    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => '上一步',
            ],

            'next_step' => [
                'label' => '下一步',
            ],

        ],

    ],

];

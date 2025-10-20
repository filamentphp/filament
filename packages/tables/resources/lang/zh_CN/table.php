<?php

return [

    'column_manager' => [

        'heading' => '列管理',

        'actions' => [

            'apply' => [
                'label' => '应用列设置',
            ],

            'reset' => [
                'label' => '重置',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => '操作',
        ],

        'select' => [

            'loading_message' => '加载中...',

            'no_search_results_message' => '无匹配选项',

            'placeholder' => '请选择',

            'searching_message' => '搜索中...',

            'search_prompt' => '输入关键词搜索...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => '收起 :count 项',
                'expand_list' => '展开 :count 项',
            ],

            'more_list_items' => '及其他 :count 项',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => '全选/取消全选本页条目',
        ],

        'bulk_select_record' => [
            'label' => '选择/取消选择条目 :key',
        ],

        'bulk_select_group' => [
            'label' => '选择/取消选择分组 :title',
        ],

        'search' => [
            'label' => '搜索',
            'placeholder' => '请输入关键词',
            'indicator' => '搜索',
        ],

    ],

    'summary' => [

        'heading' => '统计摘要',

        'subheadings' => [
            'all' => '全部 :label',
            'group' => ':group 分组统计',
            'page' => '本页数据',
        ],

        'summarizers' => [

            'average' => [
                'label' => '平均值',
            ],

            'count' => [
                'label' => '计数',
            ],

            'sum' => [
                'label' => '求和',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => '完成排序',
        ],

        'enable_reordering' => [
            'label' => '调整排序',
        ],

        'filter' => [
            'label' => '筛选',
        ],

        'group' => [
            'label' => '分组',
        ],

        'open_bulk_actions' => [
            'label' => '批量操作',
        ],

        'column_manager' => [
            'label' => '列管理',
        ],

    ],

    'empty' => [

        'heading' => '暂无 :model',

        'description' => '请创建:model以开始使用',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => '应用筛选',
            ],

            'remove' => [
                'label' => '移除筛选',
            ],

            'remove_all' => [
                'label' => '清除所有筛选',
                'tooltip' => '清除所有筛选条件',
            ],

            'reset' => [
                'label' => '重置',
            ],

        ],

        'heading' => '筛选条件',

        'indicator' => '已启用筛选',

        'multi_select' => [
            'placeholder' => '全部',
        ],

        'select' => [

            'placeholder' => '全部',

            'relationship' => [
                'empty_option_label' => '无关联项',
            ],

        ],

        'trashed' => [

            'label' => '已删除记录',

            'only_trashed' => '仅显示已删除',

            'with_trashed' => '包含已删除',

            'without_trashed' => '不含已删除',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => '分组依据',
            ],

            'direction' => [

                'label' => '排序方向',

                'options' => [
                    'asc' => '升序',
                    'desc' => '降序',
                ],

            ],

        ],

    ],

    'reorder_indicator' => '拖放条目调整排序',

    'selection_indicator' => [

        'selected_count' => '已选 :count 条记录',

        'actions' => [

            'select_all' => [
                'label' => '全选 :count 条',
            ],

            'deselect_all' => [
                'label' => '取消全选',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => '排序字段',
            ],

            'direction' => [

                'label' => '排序方式',

                'options' => [
                    'asc' => '升序',
                    'desc' => '降序',
                ],

            ],

        ],

    ],

    'default_model_label' => '记录',

];

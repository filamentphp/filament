<?php

return [

    'column_manager' => [

        'heading' => 'Сутунҳо',

        'position' => 'Мавқеи :position аз :count.',

        'position_in_group' => 'Мавқеи :position аз :count дар :group.',

        'reordered' => ':column ба мавқеи :position аз :count гузаронида шуд.',

        'reordered_deferred' => ':column ба мавқеи :position аз :count гузаронида шуд. Барои нигоҳ доштани тағйирот сутунҳоро татбиқ кунед.',

        'reordered_in_group' => ':column ба мавқеи :position аз :count дар :group гузаронида шуд.',

        'reordered_in_group_deferred' => ':column ба мавқеи :position аз :count дар :group гузаронида шуд. Барои нигоҳ доштани тағйирот сутунҳоро татбиқ кунед.',

        'actions' => [

            'apply' => [
                'label' => 'Татбиқ кардани сутунҳо',
            ],

            'move_up' => [
                'label' => 'Ба боло гузарондани :column',
            ],

            'move_down' => [
                'label' => 'Ба поён гузарондани :column',
            ],

            'reorder' => [
                'label' => 'Ҷойивазкунии сутун',
            ],

            'reset' => [
                'label' => 'Аз нав барқарор кардан',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => '{1} Амал|[2,*] Амалҳо',
        ],

        'editable' => [
            'saving' => 'Тағйирот нигоҳ дошта мешавад...',
            'saved' => 'Тағйирот нигоҳ дошта шуд.',
            'failure' => 'Нигоҳ доштан тасдиқ нашуд. Пеш аз кӯшиши дубора саҳифаро аз нав бор кунед.',
            'unavailable' => 'Ин элементро дигар таҳрир кардан мумкин нест.',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Ҳа',
                'false' => 'Не',
            ],

        ],

        'select' => [

            'loading_message' => 'Боркунӣ...',

            'no_options_message' => 'Ягон интихоб дастрас нест.',

            'no_search_results_message' => 'Ягон натиҷа ба дархости шумо мувофиқ нест.',

            'placeholder' => 'Қиматро интихоб кунед',

            'searching_message' => 'Ҷустуҷӯ...',

            'search_prompt' => 'Барои ҷустуҷӯ матн ворид кунед...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Пинҳон кардани :count',
                'expand_list' => 'Нишон додани боз :count',
            ],

            'more_list_items' => 'ва боз :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Интихоб ё бекор кардани ҳамаи элементҳо барои амалҳои гурӯҳӣ.',
        ],

        'bulk_select_record' => [
            'label' => 'Интихоб ё бекор кардани :key барои амалҳои гурӯҳӣ.',
        ],

        'bulk_select_group' => [
            'label' => 'Интихоб ё бекор кардани хулосаи :title барои амалҳои гурӯҳӣ.',
        ],

        'search' => [
            'label' => 'Ҷустуҷӯ',
            'placeholder' => 'Ҷустуҷӯ',
            'indicator' => 'Ҷустуҷӯ',
        ],

    ],

    'summary' => [

        'heading' => 'Хулоса',

        'subheadings' => [
            'all' => 'Ҳамаи :label',
            'group' => 'Хулосаи :group',
            'page' => 'Ин саҳифа',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Миёна',
            ],

            'count' => [
                'label' => 'Миқдор',
            ],

            'sum' => [
                'label' => 'Ҷамъ',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Нигоҳ доштани тартиб',
        ],

        'enable_reordering' => [
            'label' => 'Тағйир додани тартиб',
        ],

        'reorder_record' => [
            'label' => 'Ҷойивазкунии элементи :key',
        ],

        'move_record_up' => [
            'label' => 'Ба боло гузарондани :label',
        ],

        'move_record_down' => [
            'label' => 'Ба поён гузарондани :label',
        ],

        'filter' => [
            'label' => 'Филтр',
        ],

        'group' => [
            'label' => 'Гурӯҳбандӣ',
        ],

        'open_bulk_actions' => [
            'label' => 'Кушодани амалҳо',
        ],

        'column_manager' => [
            'label' => 'Идоракунии сутунҳо',
        ],

        'toggle_record_content' => [
            'label' => 'Кушодан/пинҳон кардани элементи :key',
        ],

    ],

    'empty' => [

        'heading' => ':model ёфт нашуд',

        'description' => 'Барои оғоз :model эҷод кунед.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Татбиқ кардани филтрҳо',
            ],

            'remove' => [
                'label' => 'Нест кардани филтр',
            ],

            'remove_all' => [
                'label' => 'Пок кардани филтрҳо',
                'tooltip' => 'Пок кардани филтрҳо',
            ],

            'reset' => [
                'label' => 'Аз нав барқарор кардан',
            ],

        ],

        'heading' => 'Филтрҳо',

        'indicator' => 'Филтрҳои фаъол',

        'multi_select' => [
            'placeholder' => 'Ҳама',
        ],

        'select' => [

            'placeholder' => 'Ҳама',

            'relationship' => [
                'empty_option_label' => 'Нест',
            ],

        ],

        'trashed' => [

            'label' => 'Сабтҳои ҳазфшуда',

            'only_trashed' => 'Танҳо сабтҳои ҳазфшуда',

            'with_trashed' => 'Бо сабтҳои ҳазфшуда',

            'without_trashed' => 'Бе сабтҳои ҳазфшуда',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Гурӯҳбандӣ аз рӯи',
            ],

            'direction' => [

                'label' => 'Самт',

                'options' => [
                    'asc' => 'Бо тартиби афзоиш',
                    'desc' => 'Бо тартиби коҳиш',
                ],

            ],

        ],

    ],

    'loading' => 'Бор шуда истодааст...',

    'reorder_indicator' => 'Барои тағйир додани тартиб сабтҳоро кашола кунед.',

    'reordering' => [
        'position' => ':label, мавқеи :position аз :count.',
        'moved' => ':label ба мавқеи :position аз :count гузаронида шуд.',
    ],

    'result_count' => '{0} Натиҷа нест|{1} :count натиҷа|[2,*] :count натиҷа',

    'selection_indicator' => [

        'selected_count' => ':count сабт интихоб шуд|:count сабт интихоб шуд',

        'actions' => [

            'select_all' => [
                'label' => 'Интихоб кардани ҳамаи :count',
            ],

            'deselect_all' => [
                'label' => 'Бекор кардани интихоби ҳама',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Мураттабсозӣ',
            ],

            'direction' => [

                'label' => 'Самт',

                'options' => [
                    'asc' => 'Бо тартиби афзоиш',
                    'desc' => 'Бо тартиби коҳиш',
                ],

            ],

        ],

    ],

    'default_model_label' => 'сабт',

];

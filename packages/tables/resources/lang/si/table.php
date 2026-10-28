<?php

return [

    'column_manager' => [

        'heading' => 'තීරු',

        'position' => ':count න් :position වන ස්ථානය.',

        'position_in_group' => ':group හි :count න් :position වන ස්ථානය.',

        'reordered' => ':column :count න් :position වන ස්ථානයට ගෙන යන ලදී.',

        'reordered_deferred' => ':column :count න් :position වන ස්ථානයට ගෙන යන ලදී. ඔබගේ වෙනස්කම් සුරැකීමට තීරු යොදන්න.',

        'reordered_in_group' => ':column :group හි :count න් :position වන ස්ථානයට ගෙන යන ලදී.',

        'reordered_in_group_deferred' => ':column :group හි :count න් :position වන ස්ථානයට ගෙන යන ලදී. ඔබගේ වෙනස්කම් සුරැකීමට තීරු යොදන්න.',

        'actions' => [

            'apply' => [
                'label' => 'තීරු යොදන්න',
            ],

            'move_up' => [
                'label' => ':column ඉහළට ගෙන යන්න',
            ],

            'move_down' => [
                'label' => ':column පහළට ගෙන යන්න',
            ],

            'reorder' => [
                'label' => 'තීරුව යළි පිළිවෙළ කරන්න',
            ],

            'reset' => [
                'label' => 'යළි සකසන්න',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'ක්‍රියාව|ක්‍රියා',
        ],

        'editable' => [
            'saving' => 'වෙනස්කම් සුරකිමින්...',
            'saved' => 'වෙනස්කම් සුරකින ලදී.',
            'failure' => 'සුරැකීම තහවුරු කළ නොහැකි විය. නැවත උත්සාහ කිරීමට පෙර පිටුව නැවුම් කරන්න.',
            'unavailable' => 'මෙම අයිතමය තවදුරටත් සංස්කරණය කළ නොහැක.',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'ඔව්',
                'false' => 'නැත',
            ],

        ],

        'select' => [

            'loading_message' => 'පූරණය වෙමින්...',

            'no_options_message' => 'විකල්ප කිසිවක් නැත.',

            'no_search_results_message' => 'ඔබගේ සෙවුමට ගැළපෙන කිසිවක් නැත.',

            'placeholder' => 'විකල්පයක් තෝරන්න',

            'searching_message' => 'සොයමින්...',

            'search_prompt' => 'සෙවීමට යතුරු ලියන්න',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => ':count ක් අඩුවෙන් පෙන්වන්න',
                'expand_list' => ':count ක් තවත් පෙන්වන්න',
            ],

            'more_list_items' => 'සහ තවත් :count ක්',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'තොග ක්‍රියා සඳහා සියලුම අයිතම තෝරන්න/තේරීම ඉවත් කරන්න.',
        ],

        'bulk_select_record' => [
            'label' => 'තොග ක්‍රියා සඳහා :key අයිතමය තෝරන්න/තේරීම ඉවත් කරන්න.',
        ],

        'bulk_select_group' => [
            'label' => 'තොග ක්‍රියා සඳහා :title කණ්ඩායම තෝරන්න/තේරීම ඉවත් කරන්න.',
        ],

        'search' => [
            'label' => 'සොයන්න',
            'placeholder' => 'සොයන්න',
            'indicator' => 'සොයන්න',
        ],

    ],

    'summary' => [

        'heading' => 'සාරාංශය',

        'subheadings' => [
            'all' => 'සියලුම :label',
            'group' => ':group සාරාංශය',
            'page' => 'මෙම පිටුව',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'සාමාන්‍යය',
            ],

            'count' => [
                'label' => 'ගණන',
            ],

            'sum' => [
                'label' => 'එකතුව',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'වාර්තා යළි පිළිවෙළ කිරීම අවසන් කරන්න',
        ],

        'enable_reordering' => [
            'label' => 'වාර්තා යළි පිළිවෙළ කරන්න',
        ],

        'reorder_record' => [
            'label' => ':key අයිතමය යළි පිළිවෙළ කරන්න',
        ],

        'move_record_up' => [
            'label' => ':label ඉහළට ගෙන යන්න',
        ],

        'move_record_down' => [
            'label' => ':label පහළට ගෙන යන්න',
        ],

        'filter' => [
            'label' => 'පෙරහන',
        ],

        'group' => [
            'label' => 'කණ්ඩායම',
        ],

        'open_bulk_actions' => [
            'label' => 'තොග ක්‍රියා',
        ],

        'column_manager' => [
            'label' => 'තීරු කළමනාකරු',
        ],

        'toggle_record_content' => [
            'label' => ':key අයිතමය දිගහරින්න/හකුළන්න',
        ],

    ],

    'empty' => [

        'heading' => ':model නැත',

        'description' => 'ආරම්භ කිරීමට :model එකක් සාදන්න.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'පෙරහන් යොදන්න',
            ],

            'remove' => [
                'label' => 'පෙරහන ඉවත් කරන්න',
            ],

            'remove_all' => [
                'label' => 'සියලුම පෙරහන් ඉවත් කරන්න',
                'tooltip' => 'සියලුම පෙරහන් ඉවත් කරන්න',
            ],

            'reset' => [
                'label' => 'යළි සකසන්න',
            ],

        ],

        'heading' => 'පෙරහන්',

        'indicator' => 'සක්‍රිය පෙරහන්',

        'multi_select' => [
            'placeholder' => 'සියල්ල',
        ],

        'select' => [

            'placeholder' => 'සියල්ල',

            'relationship' => [
                'empty_option_label' => 'කිසිවක් නැත',
            ],

        ],

        'trashed' => [

            'label' => 'මකා දැමූ වාර්තා',

            'only_trashed' => 'මකා දැමූ වාර්තා පමණි',

            'with_trashed' => 'මකා දැමූ වාර්තා සමඟ',

            'without_trashed' => 'මකා දැමූ වාර්තා රහිතව',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'කණ්ඩායම් කරන්න',
            ],

            'direction' => [

                'label' => 'කණ්ඩායම් දිශාව',

                'options' => [
                    'asc' => 'ආරෝහණ',
                    'desc' => 'අවරෝහණ',
                ],

            ],

        ],

    ],

    'loading' => 'පූරණය වෙමින්...',

    'reorder_indicator' => 'වාර්තා පිළිවෙළට ඇදගෙන ගොස් දමන්න.',

    'reordering' => [
        'position' => ':label, :count න් :position වන ස්ථානය.',
        'moved' => ':label :count න් :position වන ස්ථානයට ගෙන යන ලදී.',
    ],

    'result_count' => '{0} ප්‍රතිඵල නැත|{1} ප්‍රතිඵල :count ක්|[2,*] ප්‍රතිඵල :count ක්',

    'selection_indicator' => [

        'selected_count' => 'වාර්තා 1 ක් තෝරා ඇත|වාර්තා :count ක් තෝරා ඇත',

        'actions' => [

            'select_all' => [
                'label' => ':count ම තෝරන්න',
            ],

            'deselect_all' => [
                'label' => 'සියල්ල ඉවත් කරන්න',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'අනුව පිළිවෙළ කරන්න',
            ],

            'direction' => [

                'label' => 'පිළිවෙළ කිරීමේ දිශාව',

                'options' => [
                    'asc' => 'ආරෝහණ',
                    'desc' => 'අවරෝහණ',
                ],

            ],

        ],

    ],

    'default_model_label' => 'වාර්තාව',

];

<?php

return [

    'builder' => [

        'reordering' => [
            'item' => 'බ්ලොක් එක',
            'position' => ':label, :count න් :position වන ස්ථානය',
            'moved' => ':label :count න් :position වන ස්ථානයට ගෙන යන ලදී',
        ],

        'actions' => [

            'clone' => [
                'label' => 'ක්ලෝන් කරන්න',
            ],

            'add' => [

                'label' => ':label වෙත එක් කරන්න',

                'modal' => [

                    'heading' => ':label වෙත එක් කරන්න',

                    'actions' => [

                        'add' => [
                            'label' => 'එක් කරන්න',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'බ්ලොක් අතරට ඇතුළත් කරන්න',

                'modal' => [

                    'heading' => ':label වෙත එක් කරන්න',

                    'actions' => [

                        'add' => [
                            'label' => 'එක් කරන්න',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'මකන්න',
            ],

            'edit' => [

                'label' => 'සංස්කරණය',

                'modal' => [

                    'heading' => 'බ්ලොක් එක සංස්කරණය කරන්න',

                    'actions' => [

                        'save' => [
                            'label' => 'වෙනස්කම් සුරකින්න',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'ගෙන යන්න',
            ],

            'move_down' => [
                'label' => 'පහළට ගෙන යන්න',
            ],

            'move_up' => [
                'label' => 'ඉහළට ගෙන යන්න',
            ],

            'collapse' => [
                'label' => 'හකුළන්න',
            ],

            'expand' => [
                'label' => 'දිගහරින්න',
            ],

            'collapse_all' => [
                'label' => 'සියල්ල හකුළන්න',
            ],

            'expand_all' => [
                'label' => 'සියල්ල දිගහරින්න',
            ],

        ],

        'block_picker' => [

            'no_search_results_message' => 'ඔබගේ සෙවුමට ගැළපෙන බ්ලොක් නැත.',

            'search_prompt' => 'බ්ලොක් සොයන්න',

        ],

    ],

    'checkbox_list' => [

        'required_description' => 'අවම වශයෙන් එක් විකල්පයක් තෝරන්න.',

        'actions' => [

            'deselect_all' => [
                'label' => 'සියල්ල ඉවත් කරන්න',
            ],

            'select_all' => [
                'label' => 'සියල්ල තෝරන්න',
            ],

        ],

    ],

    'color_picker' => [

        'panel_label' => 'වර්ණ තෝරකය',

    ],

    'date_time_picker' => [

        'month_select' => [
            'label' => 'මාසය',
        ],

        'year_input' => [
            'label' => 'වසර',
        ],

        'hour_input' => [
            'label' => 'පැය',
        ],

        'minute_input' => [
            'label' => 'මිනිත්තු',
        ],

        'second_input' => [
            'label' => 'තත්පර',
        ],

    ],

    'file_upload' => [

        'placeholder' => 'ගොනු උඩුගත කර නැත',

        'actions' => [

            'download' => [
                'label' => 'බාගන්න',
            ],

            'open' => [
                'label' => 'නව ටැබයක විවෘත කරන්න',
            ],

        ],

        'editor' => [

            'label' => 'රූප සංස්කාරකය',

            'actions' => [

                'cancel' => [
                    'label' => 'අවලංගු කරන්න',
                ],

                'drag_crop' => [
                    'label' => '"කපන්න" ඇදීමේ ප්‍රකාරය',
                ],

                'drag_move' => [
                    'label' => '"ගෙන යන්න" ඇදීමේ ප්‍රකාරය',
                ],

                'flip_horizontal' => [
                    'label' => 'රූපය තිරස් අතට පෙරළන්න',
                ],

                'flip_vertical' => [
                    'label' => 'රූපය සිරස් අතට පෙරළන්න',
                ],

                'move_down' => [
                    'label' => 'රූපය පහළට ගෙන යන්න',
                ],

                'move_left' => [
                    'label' => 'රූපය වමට ගෙන යන්න',
                ],

                'move_right' => [
                    'label' => 'රූපය දකුණට ගෙන යන්න',
                ],

                'move_up' => [
                    'label' => 'රූපය ඉහළට ගෙන යන්න',
                ],

                'reset' => [
                    'label' => 'යළි සකසන්න',
                ],

                'rotate_left' => [
                    'label' => 'රූපය වමට කරකවන්න',
                ],

                'rotate_right' => [
                    'label' => 'රූපය දකුණට කරකවන්න',
                ],

                'set_aspect_ratio' => [
                    'label' => 'දර්ශන අනුපාතය :ratio ලෙස සකසන්න',
                ],

                'save' => [
                    'label' => 'සුරකින්න',
                ],

                'zoom_100' => [
                    'label' => 'රූපය 100% ට විශාල කරන්න',
                ],

                'zoom_in' => [
                    'label' => 'විශාල කරන්න',
                ],

                'zoom_out' => [
                    'label' => 'කුඩා කරන්න',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'උස',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'භ්‍රමණය',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'පළල',
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

                'label' => 'දර්ශන අනුපාත',

                'no_fixed' => [
                    'label' => 'නිදහස්',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG ගොනු සංස්කරණය කිරීම නිර්දේශ නොකරයි, මන්ද විශාල කිරීමේදී එය ගුණාත්මක භාවය අඩු කළ හැකිය.\n ඔබට දිගටම කරගෙන යාමට විශ්වාසද?',
                    'disabled' => 'SVG ගොනු සංස්කරණය අක්‍රිය කර ඇත, මන්ද විශාල කිරීමේදී එය ගුණාත්මක භාවය අඩු කළ හැකිය.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'පේළියක් එක් කරන්න',
            ],

            'delete' => [
                'label' => 'පේළිය මකන්න',
            ],

            'move_down' => [
                'label' => 'පහළට ගෙන යන්න',
            ],

            'move_up' => [
                'label' => 'ඉහළට ගෙන යන්න',
            ],

            'reorder' => [
                'label' => 'පේළිය යළි පිළිවෙළ කරන්න',
            ],

        ],

        'columns' => [

            'actions' => [
                'label' => 'ක්‍රියා',
            ],

            'reorder' => [
                'label' => 'යළි පිළිවෙළ කරන්න',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'යතුර',
            ],

            'value' => [
                'label' => 'අගය',
            ],

        ],

        'reordering' => [
            'row' => 'පේළිය',
            'position' => ':label, :count න් :position වන ස්ථානය',
            'moved' => ':label :count න් :position වන ස්ථානයට ගෙන යන ලදී',
        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'උඩුගත කරන ගොනු මෙම වර්ගයේ විය යුතුය: :values.',

        'file_attachments_max_size_message' => 'උඩුගත කරන ගොනු කිලෝබයිට් :max ට වඩා විශාල නොවිය යුතුය.',

        'tools' => [
            'attach_files' => 'ගොනු අමුණන්න',
            'blockquote' => 'උද්ධෘත බ්ලොක් එක',
            'bold' => 'තද අකුරු',
            'bullet_list' => 'ලකුණු ලැයිස්තුව',
            'code_block' => 'කේත බ්ලොක් එක',
            'heading' => 'ශීර්ෂය',
            'italic' => 'ඇල අකුරු',
            'link' => 'සබැඳිය',
            'ordered_list' => 'අංකිත ලැයිස්තුව',
            'redo' => 'නැවත කරන්න',
            'strike' => 'කැපුම් ඉර',
            'table' => 'වගුව',
            'undo' => 'පසුගමනය',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'තෝරන්න',

                'actions' => [

                    'select' => [
                        'label' => 'තෝරන්න',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'ඔව්',
            'false' => 'නැත',
        ],

    ],

    'repeater' => [

        'reordering' => [
            'item' => 'අයිතමය',
            'position' => ':label, :count න් :position වන ස්ථානය',
            'moved' => ':label :count න් :position වන ස්ථානයට ගෙන යන ලදී',
        ],

        'columns' => [

            'actions' => [
                'label' => 'ක්‍රියා',
            ],

            'reorder' => [
                'label' => 'යළි පිළිවෙළ කරන්න',
            ],

        ],

        'actions' => [

            'add' => [
                'label' => ':label වෙත එක් කරන්න',
            ],

            'add_between' => [
                'label' => 'අතරට ඇතුළත් කරන්න',
            ],

            'delete' => [
                'label' => 'මකන්න',
            ],

            'clone' => [
                'label' => 'ක්ලෝන් කරන්න',
            ],

            'reorder' => [
                'label' => 'ගෙන යන්න',
            ],

            'move_down' => [
                'label' => 'පහළට ගෙන යන්න',
            ],

            'move_up' => [
                'label' => 'ඉහළට ගෙන යන්න',
            ],

            'collapse' => [
                'label' => 'හකුළන්න',
            ],

            'expand' => [
                'label' => 'දිගහරින්න',
            ],

            'collapse_all' => [
                'label' => 'සියල්ල හකුළන්න',
            ],

            'expand_all' => [
                'label' => 'සියල්ල දිගහරින්න',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'ගොනුවක් උඩුගත කරන්න',

                'modal' => [

                    'heading' => 'ගොනුවක් උඩුගත කරන්න',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'ගොනුව',
                                'existing' => 'ගොනුව ප්‍රතිස්ථාපනය කරන්න',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'විකල්ප පෙළ',
                                'existing' => 'විකල්ප පෙළ වෙනස් කරන්න',
                            ],

                        ],

                    ],

                ],

            ],

            'close_panel' => [
                'label' => 'පුවරුව වසන්න',
            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'ඇතුළත් කරන්න',
                        ],

                        'save' => [
                            'label' => 'සුරකින්න',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'ජාලය',

                'modal' => [

                    'heading' => 'ජාලය',

                    'form' => [

                        'preset' => [

                            'label' => 'පූර්ව සැකසුම',

                            'placeholder' => 'කිසිවක් නැත',

                            'options' => [
                                'two' => 'දෙක',
                                'three' => 'තුන',
                                'four' => 'හතර',
                                'five' => 'පහ',
                                'two_start_third' => 'දෙක (ආරම්භයේ තුනෙන් එකක්)',
                                'two_end_third' => 'දෙක (අවසානයේ තුනෙන් එකක්)',
                                'two_start_fourth' => 'දෙක (ආරම්භයේ හතරෙන් එකක්)',
                                'two_end_fourth' => 'දෙක (අවසානයේ හතරෙන් එකක්)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'තීරු',
                        ],

                        'from_breakpoint' => [

                            'label' => 'බිඳුම් ලක්ෂ්‍යයෙන් පටන්',

                            'options' => [
                                'default' => 'සියල්ල',
                                'sm' => 'කුඩා',
                                'md' => 'මධ්‍යම',
                                'lg' => 'විශාල',
                                'xl' => 'අතිවිශාල',
                                '2xl' => 'ද්විත්ව අතිවිශාල',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'අසමමිතික තීරු දෙකක්',
                        ],

                        'start_span' => [
                            'label' => 'ආරම්භක විහිදුම',
                        ],

                        'end_span' => [
                            'label' => 'අවසාන විහිදුම',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'සබැඳිය',

                'modal' => [

                    'heading' => 'සබැඳිය',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'නව ටැබයක විවෘත කරන්න',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'අකුරු වර්ණය',

                'modal' => [

                    'heading' => 'අකුරු වර්ණය',

                    'form' => [

                        'color' => [
                            'label' => 'වර්ණය',

                            'options' => [
                                'slate' => 'ස්ලේට්',
                                'gray' => 'අළු',
                                'zinc' => 'සින්ක්',
                                'neutral' => 'මධ්‍යස්ථ',
                                'stone' => 'ගල් පැහැය',
                                'mauve' => 'මව්ව්',
                                'olive' => 'ඔලිව්',
                                'mist' => 'මිස්ට්',
                                'taupe' => 'ටෝප්',
                                'red' => 'රතු',
                                'orange' => 'තැඹිලි',
                                'amber' => 'ඇම්බර්',
                                'yellow' => 'කහ',
                                'lime' => 'ලයිම්',
                                'green' => 'කොළ',
                                'emerald' => 'එමරල්ඩ්',
                                'teal' => 'ටීල්',
                                'cyan' => 'සියන්',
                                'sky' => 'අහස් නිල්',
                                'blue' => 'නිල්',
                                'indigo' => 'ඉන්ඩිගෝ',
                                'violet' => 'වයලට්',
                                'purple' => 'දම්',
                                'fuchsia' => 'ෆුෂියා',
                                'pink' => 'ලා රෝස',
                                'rose' => 'රෝස්',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'අභිරුචි වර්ණය',
                        ],

                    ],

                ],

            ],

        ],

        'custom_blocks' => [

            'actions' => [

                'delete' => [
                    'label' => 'බ්ලොක් එක මකන්න',
                ],

                'edit' => [
                    'label' => 'බ්ලොක් එක සංස්කරණය කරන්න',
                ],

            ],

            'no_search_results_message' => 'ඔබගේ සෙවුමට ගැළපෙන බ්ලොක් නැත.',

            'search_label' => 'බ්ලොක් සොයන්න',

            'search_prompt' => 'බ්ලොක් සොයන්න',

        ],

        'file_attachments_accepted_file_types_message' => 'උඩුගත කරන ගොනු මෙම වර්ගයේ විය යුතුය: :values.',

        'file_attachments_max_size_message' => 'උඩුගත කරන ගොනු කිලෝබයිට් :max ට වඩා විශාල නොවිය යුතුය.',

        'no_merge_tag_search_results_message' => 'ඒකාබද්ධ ටැග් ප්‍රතිඵල නැත.',

        'mentions' => [
            'no_options_message' => 'විකල්ප කිසිවක් නැත.',
            'no_search_results_message' => 'ඔබගේ සෙවුමට ගැළපෙන ප්‍රතිඵල නැත.',
            'search_prompt' => 'සෙවීමට යතුරු ලියන්න',
            'searching_message' => 'සොයමින්...',
        ],

        'toolbar' => [
            'label' => 'සංස්කාරක මෙවලම් තීරුව',
        ],

        'tools' => [
            'align_center' => 'මැදට පෙළගස්වන්න',
            'align_end' => 'අවසානයට පෙළගස්වන්න',
            'align_justify' => 'සමානව පෙළගස්වන්න',
            'align_start' => 'ආරම්භයට පෙළගස්වන්න',
            'attach_files' => 'ගොනු අමුණන්න',
            'blockquote' => 'උද්ධෘත බ්ලොක් එක',
            'bold' => 'තද අකුරු',
            'bullet_list' => 'ලකුණු ලැයිස්තුව',
            'clear_formatting' => 'හැඩතල ගැන්වීම ඉවත් කරන්න',
            'code' => 'කේතය',
            'code_block' => 'කේත බ්ලොක් එක',
            'custom_blocks' => 'බ්ලොක්',
            'details' => 'විස්තර',
            'h1' => 'මාතෘකාව',
            'h2' => 'ශීර්ෂය 2',
            'h3' => 'ශීර්ෂය 3',
            'h4' => 'ශීර්ෂය 4',
            'h5' => 'ශීර්ෂය 5',
            'h6' => 'ශීර්ෂය 6',
            'grid' => 'ජාලය',
            'grid_delete' => 'ජාලය මකන්න',
            'highlight' => 'ඉස්මතු කරන්න',
            'horizontal_rule' => 'තිරස් රේඛාව',
            'italic' => 'ඇල අකුරු',
            'lead' => 'ප්‍රමුඛ පෙළ',
            'link' => 'සබැඳිය',
            'merge_tags' => 'ඒකාබද්ධ ටැග්',
            'ordered_list' => 'අංකිත ලැයිස්තුව',
            'paragraph' => 'ඡේදය',
            'redo' => 'නැවත කරන්න',
            'small' => 'කුඩා පෙළ',
            'strike' => 'කැපුම් ඉර',
            'subscript' => 'උපසරණය',
            'superscript' => 'අධිසරණය',
            'table' => 'වගුව',
            'table_delete' => 'වගුව මකන්න',
            'table_add_column_before' => 'පෙර තීරුවක් එක් කරන්න',
            'table_add_column_after' => 'පසු තීරුවක් එක් කරන්න',
            'table_delete_column' => 'තීරුව මකන්න',
            'table_add_row_before' => 'ඉහළින් පේළියක් එක් කරන්න',
            'table_add_row_after' => 'පහළින් පේළියක් එක් කරන්න',
            'table_delete_row' => 'පේළිය මකන්න',
            'table_merge_cells' => 'කොටු ඒකාබද්ධ කරන්න',
            'table_split_cell' => 'කොටුව බෙදන්න',
            'table_toggle_header_row' => 'ශීර්ෂ පේළිය මාරු කරන්න',
            'table_toggle_header_cell' => 'ශීර්ෂ කොටුව මාරු කරන්න',
            'text_color' => 'අකුරු වර්ණය',
            'underline' => 'යටින් ඉර',
            'undo' => 'පසුගමනය',
        ],

        'uploading_file_message' => 'ගොනුව උඩුගත කරමින්...',

    ],

    'select' => [

        'actions' => [

            'clear' => [
                'label' => 'තේරීම හිස් කරන්න',
            ],

            'create_option' => [

                'label' => 'සාදන්න',

                'modal' => [

                    'heading' => 'සාදන්න',

                    'actions' => [

                        'create' => [
                            'label' => 'සාදන්න',
                        ],

                        'create_another' => [
                            'label' => 'සාදා තවත් එකක් සාදන්න',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'සංස්කරණය',

                'modal' => [

                    'heading' => 'සංස්කරණය',

                    'actions' => [

                        'save' => [
                            'label' => 'සුරකින්න',
                        ],

                    ],

                ],

            ],

            'remove_option' => [
                'label' => ':label ඉවත් කරන්න',
            ],

        ],

        'boolean' => [
            'true' => 'ඔව්',
            'false' => 'නැත',
        ],

        'error_message' => 'විකල්ප පූරණය කළ නොහැකි විය. කරුණාකර නැවත උත්සාහ කරන්න.',

        'loading_message' => 'පූරණය වෙමින්...',

        'max_items_message' => ':count ක් පමණක් තෝරාගත හැක.',

        'no_options_message' => 'විකල්ප කිසිවක් නැත.',

        'no_search_results_message' => 'ඔබගේ සෙවුමට ගැළපෙන විකල්ප නැත.',

        'placeholder' => 'විකල්පයක් තෝරන්න',

        'required_description' => 'විකල්පයක් තෝරන්න.',

        'searching_message' => 'සොයමින්...',

        'search_label' => 'සොයන්න',

        'search_prompt' => 'සෙවීමට යතුරු ලියන්න',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'මකන්න',
            ],

        ],

        'placeholder' => 'නව ටැගය',

        'tag_added' => 'එක් කරන ලදී: :tag',

        'tag_removed' => 'ඉවත් කරන ලදී: :tag',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'පිටපත් කරන්න',
                'message' => 'පිටපත් කරන ලදී',
            ],

            'hide_password' => [
                'label' => 'මුරපදය සඟවන්න',
            ],

            'show_password' => [
                'label' => 'මුරපදය පෙන්වන්න',
            ],

        ],

    ],

    'toggle_buttons' => [

        'required_description' => 'අවම වශයෙන් එක් විකල්පයක් තෝරන්න.',

        'boolean' => [
            'true' => 'ඔව්',
            'false' => 'නැත',
        ],

    ],

];

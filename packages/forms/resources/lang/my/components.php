<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'ပုံတူကူးရန်',
            ],

            'add' => [
                'label' => ':label သို့ ထည့်သွင်းရန်',

                'modal' => [
                    'heading' => ':label သို့ ထည့်သွင်းရန်',
                    'actions' => [
                        'add' => [
                            'label' => 'ထည့်သွင်းရန်',
                        ],
                    ],
                ],
            ],

            'add_between' => [
                'label' => 'အပိုင်းများကြားတွင် ထည့်သွင်းရန်',
                'modal' => [
                    'heading' => ':label သို့ ထည့်သွင်းရန်',
                    'actions' => [
                        'add' => [
                            'label' => 'ထည့်သွင်းရန်',
                        ],
                    ],
                ],
            ],

            'delete' => [
                'label' => 'ဖျက်ရန်',
            ],

            'edit' => [
                'label' => 'ပြင်ဆင်ရန်',
                'modal' => [
                    'heading' => 'အပိုင်းကို ပြင်ဆင်ရန်',
                    'actions' => [
                        'save' => [
                            'label' => 'အပြောင်းအလဲများကို သိမ်းဆည်းရန်',
                        ],
                    ],
                ],
            ],

            'reorder' => [
                'label' => 'ရွှေ့ရန်',
            ],

            'move_down' => [
                'label' => 'အောက်သို့ ရွှေ့ရန်',
            ],

            'move_up' => [
                'label' => 'အပေါ်သို့ ရွှေ့ရန်',
            ],

            'collapse' => [
                'label' => 'ချုံ့ရန်',
            ],

            'expand' => [
                'label' => 'ချဲ့ရန်',
            ],

            'collapse_all' => [
                'label' => 'အားလုံးချုံ့ရန်',
            ],

            'expand_all' => [
                'label' => 'အားလုံးချဲ့ရန်',
            ],
        ],
    ],

    'checkbox_list' => [
        'actions' => [
            'deselect_all' => [
                'label' => 'အားလုံးရွေးချယ်ခြင်းမှ ဖယ်ရှားရန်',
            ],
            'select_all' => [
                'label' => 'အားလုံးရွေးချယ်ရန်',
            ],
        ],
    ],

    'file_upload' => [
        'editor' => [
            'actions' => [
                'cancel' => [
                    'label' => 'ပယ်ဖျက်ရန်',
                ],
                'drag_crop' => [
                    'label' => 'ဖြတ်ညှပ်ရန် ဆွဲချမုဒ်',
                ],
                'drag_move' => [
                    'label' => 'ရွှေ့ရန် ဆွဲချမုဒ်',
                ],
                'flip_horizontal' => [
                    'label' => 'ပုံကို အလျားလိုက် ပြောင်းပြန်လှန်ရန်',
                ],
                'flip_vertical' => [
                    'label' => 'ပုံကို ဒေါင်လိုက် ပြောင်းပြန်လှန်ရန်',
                ],
                'move_down' => [
                    'label' => 'ပုံကို အောက်သို့ရွှေ့ရန်',
                ],
                'move_left' => [
                    'label' => 'ပုံကို ဘယ်ဘက်သို့ရွှေ့ရန်',
                ],
                'move_right' => [
                    'label' => 'ပုံကို ညာဘက်သို့ရွှေ့ရန်',
                ],
                'move_up' => [
                    'label' => 'ပုံကို အပေါ်သို့ရွှေ့ရန်',
                ],
                'reset' => [
                    'label' => 'ပြန်လည်သတ်မှတ်ရန်',
                ],
                'rotate_left' => [
                    'label' => 'ပုံကို ဘယ်ဘက်သို့လှည့်ရန်',
                ],
                'rotate_right' => [
                    'label' => 'ပုံကို ညာဘက်သို့လှည့်ရန်',
                ],
                'set_aspect_ratio' => [
                    'label' => 'အချိုးကို :ratio သို့ သတ်မှတ်ရန်',
                ],
                'save' => [
                    'label' => 'သိမ်းဆည်းရန်',
                ],
                'zoom_100' => [
                    'label' => 'ပုံကို ၁၀၀% ချဲ့ရန်',
                ],
                'zoom_in' => [
                    'label' => 'ချဲ့ရန်',
                ],
                'zoom_out' => [
                    'label' => 'ချုံ့ရန်',
                ],
            ],

            'fields' => [
                'height' => [
                    'label' => 'အမြင့်',
                    'unit' => 'px',
                ],
                'rotation' => [
                    'label' => 'လှည့်ခြင်း',
                    'unit' => 'deg',
                ],
                'width' => [
                    'label' => 'အကျယ်',
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
                'label' => 'အချိုးများ',
                'no_fixed' => [
                    'label' => 'အသင့်အတင့်',
                ],
            ],

            'svg' => [
                'messages' => [
                    'confirmation' => 'SVG ဖိုင်များကို တည်းဖြတ်ခြင်းသည် အရွယ်အစားချဲ့သည့်အခါ အရည်အသွေးကျဆင်းခြင်းကို ဖြစ်စေနိုင်သောကြောင့် အကြံပြုထားခြင်း မရှိပါ။\n ဆက်လက်လုပ်ဆောင်ရန် သေချာပါသလား။',
                    'disabled' => 'SVG ဖိုင်များကို တည်းဖြတ်ခြင်းသည် အရွယ်အစားချဲ့သည့်အခါ အရည်အသွေးကျဆင်းခြင်းကို ဖြစ်စေနိုင်သောကြောင့် ပိတ်ထားပါသည်။',
                ],
            ],
        ],
    ],

    'key_value' => [
        'actions' => [
            'add' => [
                'label' => 'အတန်း ထည့်သွင်းရန်',
            ],
            'delete' => [
                'label' => 'အတန်း ဖျက်ရန်',
            ],
            'reorder' => [
                'label' => 'အတန်း ပြန်စီရန်',
            ],
        ],

        'fields' => [
            'key' => [
                'label' => 'သော့',
            ],
            'value' => [
                'label' => 'တန်ဖိုး',
            ],
        ],
    ],

    'markdown_editor' => [
        'toolbar_buttons' => [
            'attach_files' => 'ဖိုင်များ ပူးတွဲရန်',
            'blockquote' => 'ကိုးကားချက်',
            'bold' => 'စာလုံးထူ',
            'bullet_list' => 'အမှတ်အသားစာရင်း',
            'code_block' => 'ကုဒ်အပိုင်း',
            'heading' => 'ခေါင်းစဉ်',
            'italic' => 'စာလုံးစောင်း',
            'link' => 'လင့်ခ်',
            'ordered_list' => 'နံပါတ်တပ်စာရင်း',
            'redo' => 'ပြန်လုပ်ရန်',
            'strike' => 'လျှောက်ဖျက်မျဉ်း',
            'table' => 'ဇယား',
            'undo' => 'နောက်ပြန်ဆုတ်ရန်',
        ],
    ],

    'radio' => [

        'boolean' => [
            'true' => 'Yes',
            'false' => 'No',
        ],

    ],

    'repeater' => [
        'actions' => [
            'add' => [
                'label' => ':label သို့ ထည့်သွင်းရန်',
            ],
            'add_between' => [
                'label' => 'အပိုင်းများကြားတွင် ထည့်သွင်းရန်',
            ],
            'delete' => [
                'label' => 'ဖျက်ရန်',
            ],
            'clone' => [
                'label' => 'ပုံတူကူးရန်',
            ],
            'reorder' => [
                'label' => 'ရွှေ့ရန်',
            ],
            'move_down' => [
                'label' => 'အောက်သို့ရွှေ့ရန်',
            ],
            'move_up' => [
                'label' => 'အပေါ်သို့ရွှေ့ရန်',
            ],
            'collapse' => [
                'label' => 'ချုံ့ရန်',
            ],
            'expand' => [
                'label' => 'ချဲ့ရန်',
            ],
            'collapse_all' => [
                'label' => 'အားလုံးချုံ့ရန်',
            ],
            'expand_all' => [
                'label' => 'အားလုံးချဲ့ရန်',
            ],
        ],
    ],

    'rich_editor' => [
        'dialogs' => [
            'link' => [
                'actions' => [
                    'link' => 'လင့်ခ်',
                    'unlink' => 'လင့်ခ်ဖြုတ်ရန်',
                ],
                'label' => 'URL',
                'placeholder' => 'URL ထည့်သွင်းပါ',
            ],
        ],

        'toolbar_buttons' => [
            'attach_files' => 'ဖိုင်များ ပူးတွဲရန်',
            'blockquote' => 'ကိုးကားချက်',
            'bold' => 'စာလုံးထူ',
            'bullet_list' => 'အမှတ်အသားစာရင်း',
            'code_block' => 'ကုဒ်အပိုင်း',
            'h1' => 'ခေါင်းစဉ်',
            'h2' => 'ခေါင်းစဉ်ခွဲ',
            'h3' => 'ခေါင်းစဉ်ခွဲငယ်',
            'italic' => 'စာလုံးစောင်း',
            'link' => 'လင့်ခ်',
            'ordered_list' => 'နံပါတ်တပ်စာရင်း',
            'redo' => 'ပြန်လုပ်ရန်',
            'strike' => 'လျှောက်ဖျက်မျဉ်း',
            'underline' => 'အောက်လိုင်း',
            'undo' => 'နောက်ပြန်ဆုတ်ရန်',
        ],
    ],

    'select' => [
        'actions' => [
            'create_option' => [
                'label' => 'အသစ်ဖန်တီးရန်',
                'modal' => [
                    'heading' => 'အသစ်ဖန်တီးရန်',
                    'actions' => [
                        'create' => [
                            'label' => 'ဖန်တီးရန်',
                        ],
                        'create_another' => [
                            'label' => 'ဖန်တီးပြီး နောက်တစ်ခုဖန်တီးရန်',
                        ],
                    ],
                ],
            ],

            'edit_option' => [
                'label' => 'ပြင်ဆင်ရန်',
                'modal' => [
                    'heading' => 'ပြင်ဆင်ရန်',
                    'actions' => [
                        'save' => [
                            'label' => 'သိမ်းဆည်းရန်',
                        ],
                    ],
                ],
            ],
        ],

        'boolean' => [
            'true' => 'ဟုတ်',
            'false' => 'မဟုတ်',
        ],

        'loading_message' => 'ဖွင့်နေသည်...',
        'max_items_message' => ':count ခုသာ ရွေးချယ်နိုင်ပါသည်။',
        'no_search_results_message' => 'ရှာဖွေမှုနှင့် ကိုက်ညီသော ရွေးချယ်စရာများ မရှိပါ။',
        'placeholder' => 'ရွေးချယ်စရာတစ်ခုကို ရွေးချယ်ပါ',
        'searching_message' => 'ရှာဖွေနေသည်...',
        'search_prompt' => 'ရှာဖွေရန် စာရိုက်ထည့်ပါ...',
    ],

    'tags_input' => [
        'placeholder' => 'တဂ်အသစ်',
    ],

    'wizard' => [
        'actions' => [
            'previous_step' => [
                'label' => 'နောက်သို့',
            ],
            'next_step' => [
                'label' => 'ရှေ့သို့',
            ],
        ],
    ],

    'text_input' => [
        'actions' => [
            'hide_password' => [
                'label' => 'စကားဝှက်ဖျောက်ရန်',
            ],
            'show_password' => [
                'label' => 'စကားဝှက်ပြရန်',
            ],
        ],
    ],

    'toggle_buttons' => [
        'boolean' => [
            'true' => 'ဟုတ်',
            'false' => 'မဟုတ်',
        ],
    ],

];

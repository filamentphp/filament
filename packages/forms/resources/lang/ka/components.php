<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'კლონირება',
            ],

            'add' => [

                'label' => 'დამატება :label-ში',

                'modal' => [

                    'heading' => 'დამატება :label-ში',

                    'actions' => [

                        'add' => [
                            'label' => 'დამატება',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'ბლოკებს შორის ჩასმა',

                'modal' => [

                    'heading' => 'დამატება :label-ში',

                    'actions' => [

                        'add' => [
                            'label' => 'დამატება',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'წაშლა',
            ],

            'edit' => [

                'label' => 'რედაქტირება',

                'modal' => [

                    'heading' => 'ბლოკის რედაქტირება',

                    'actions' => [

                        'save' => [
                            'label' => 'ცვლილებების შენახვა',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'გადაადგილება',
            ],

            'move_down' => [
                'label' => 'გადაადგილება ქვემოთ',
            ],

            'move_up' => [
                'label' => 'გადაადგილება ზემოთ',
            ],

            'collapse' => [
                'label' => 'შეკუმშვა',
            ],

            'expand' => [
                'label' => 'გაფართოება',
            ],

            'collapse_all' => [
                'label' => 'ყველას შეკუმშვა',
            ],

            'expand_all' => [
                'label' => 'ყველას გაფართოება',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'ყველას არჩევის გაუქმება',
            ],

            'select_all' => [
                'label' => 'ყველას არჩევა',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'გაუქმება',
                ],

                'drag_crop' => [
                    'label' => 'რეჟიმი "მოჭრა"',
                ],

                'drag_move' => [
                    'label' => 'რეჟიმი "გადაადგილება"',
                ],

                'flip_horizontal' => [
                    'label' => 'სურათის ჰორიზონტალური გადატრიალება',
                ],

                'flip_vertical' => [
                    'label' => 'სურათის ვერტიკალური გადატრიალება',
                ],

                'move_down' => [
                    'label' => 'სურათის გადაადგილება ქვემოთ',
                ],

                'move_left' => [
                    'label' => 'სურათის გადაადგილება მარცხნივ',
                ],

                'move_right' => [
                    'label' => 'სურათის გადაადგილება მარჯვნივ',
                ],

                'move_up' => [
                    'label' => 'სურათის გადაადგილება ზემოთ',
                ],

                'reset' => [
                    'label' => 'გადატვირთვა',
                ],

                'rotate_left' => [
                    'label' => 'სურათის მარცხნივ გადატრიალება',
                ],

                'rotate_right' => [
                    'label' => 'სურათის მარჯვნივ გადატრიალება',
                ],

                'set_aspect_ratio' => [
                    'label' => 'პროპორციის დაყენება :ratio',
                ],

                'save' => [
                    'label' => 'შენახვა',
                ],

                'zoom_100' => [
                    'label' => 'ზუმი 100%',
                ],

                'zoom_in' => [
                    'label' => 'ზუმირება',
                ],

                'zoom_out' => [
                    'label' => 'გამოზუმვა',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'სიმაღლე',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'ბრუნვა',
                    'unit' => 'გრად',
                ],

                'width' => [
                    'label' => 'სიგანე',
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

                'label' => 'პროპორციები',

                'no_fixed' => [
                    'label' => 'თავისუფალი',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG ფაილების რედაქტირება არ არის რეკომენდებული, რადგან მასშტაბირებისას შეიძლება ხარისხის დაკარგვა.
 დარწმუნებული ხართ, რომ გსურთ გაგრძელება?',
                    'disabled' => 'SVG ფაილების რედაქტირება გამორთულია, რადგან მასშტაბირებისას შეიძლება ხარისხის დაკარგვა.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'ჩანაწერის დამატება',
            ],

            'delete' => [
                'label' => 'ჩანაწერის წაშლა',
            ],

            'reorder' => [
                'label' => 'ჩანაწერის გადალაგება',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'იდენტიფიკატორი',
            ],

            'value' => [
                'label' => 'მნიშვნელობა',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'ფაილების მიმაგრება',
            'blockquote' => 'ციტატა',
            'bold' => 'მსხვილი',
            'bullet_list' => 'მარკირებული სია',
            'code_block' => 'კოდის ბლოკი',
            'heading' => 'სათაური',
            'italic' => 'ტალღოვანი',
            'link' => 'ბმული',
            'ordered_list' => 'ნუმერირებული სია',
            'redo' => 'გადამეორება',
            'strike' => 'გადახაზული',
            'table' => 'ცხრილი',
            'undo' => 'გაუქმება',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'კი',
            'false' => 'არა',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'დამატება :label-ში',
            ],

            'add_between' => [
                'label' => 'ჩასმა შორის',
            ],

            'delete' => [
                'label' => 'წაშლა',
            ],

            'clone' => [
                'label' => 'კლონირება',
            ],

            'reorder' => [
                'label' => 'გადაადგილება',
            ],

            'move_down' => [
                'label' => 'გადაადგილება ქვემოთ',
            ],

            'move_up' => [
                'label' => 'გადაადგილება ზემოთ',
            ],

            'collapse' => [
                'label' => 'შეკუმშვა',
            ],

            'expand' => [
                'label' => 'გაფართოება',
            ],

            'collapse_all' => [
                'label' => 'ყველას შეკუმშვა',
            ],

            'expand_all' => [
                'label' => 'ყველას გაფართოება',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'ბმული',
                    'unlink' => 'ბმულის მოხსნა',
                ],

                'label' => 'URL',

                'placeholder' => 'შეიყვანეთ URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'ფაილების მიმაგრება',
            'blockquote' => 'ციტატა',
            'bold' => 'მსხვილი',
            'bullet_list' => 'მარკირებული სია',
            'code_block' => 'კოდის ბლოკი',
            'h1' => 'სათაური',
            'h2' => 'სათაური 2',
            'h3' => 'ქვესათაური',
            'italic' => 'ტალღოვანი',
            'link' => 'ბმული',
            'ordered_list' => 'ნუმერირებული სია',
            'redo' => 'გადამეორება',
            'strike' => 'გადახაზული',
            'underline' => 'დახაზული',
            'undo' => 'გაუქმება',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'შექმნა',

                    'actions' => [

                        'create' => [
                            'label' => 'შექმნა',
                        ],

                        'create_another' => [
                            'label' => 'შექმნა & ახალი შექმნა',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'რედაქტირება',

                    'actions' => [

                        'save' => [
                            'label' => 'შენახვა',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'კი',
            'false' => 'არა',
        ],

        'loading_message' => 'იტვირთება...',

        'max_items_message' => 'შეიძლება მხოლოდ :count აირჩიოთ.',

        'no_search_results_message' => 'არჩევანი ვერ მოიძებნა.',

        'placeholder' => 'აირჩიეთ ვარიანტი',

        'searching_message' => 'ძიება...',

        'search_prompt' => 'მოძიების დასაწყებად აკრიფეთ...',

    ],

    'tags_input' => [
        'placeholder' => 'ახალი თაგი',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'პაროლის დამალვა',
            ],

            'show_password' => [
                'label' => 'პაროლის ჩვენება',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'კი',
            'false' => 'არა',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'უკან',
            ],

            'next_step' => [
                'label' => 'შემდეგი',
            ],

        ],

    ],

];

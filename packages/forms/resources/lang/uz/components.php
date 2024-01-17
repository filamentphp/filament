<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klon',
            ],

            'add' => [
                'label' => 'Qo\'shish :label ga',
            ],

            'add_between' => [
                'label' => 'Blok orasiga qo\'shish',
            ],

            'delete' => [
                'label' => 'O\'chirish',
            ],

            'reorder' => [
                'label' => 'Ko\'chirish',
            ],

            'move_down' => [
                'label' => 'Pastga ko\'chirish',
            ],

            'move_up' => [
                'label' => 'Yuqoriga ko\'chirish',
            ],

            'collapse' => [
                'label' => 'Yig\'ish',
            ],

            'expand' => [
                'label' => 'Kengaytirish',
            ],

            'collapse_all' => [
                'label' => 'Hammasini yig\'ish',
            ],

            'expand_all' => [
                'label' => 'Hammasini kengaytirish',
            ],

        ],

    ],
    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Create',

                    'actions' => [

                        'create' => [
                            'label' => 'Create',
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
            'true' => 'Yes',
            'false' => 'No',
        ],

        'loading_message' => 'Loading...',

        'max_items_message' => 'Only :count can be selected.',

        'no_search_results_message' => 'No options match your search.',

        'placeholder' => 'Select an option',

        'searching_message' => 'Searching...',

        'search_prompt' => 'Start typing to search...',

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Barchasini tanlashni bekor qilish',
            ],

            'select_all' => [
                'label' => 'Barchasini tanlash',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Bekor qilish',
                ],

                'drag_crop' => [
                    'label' => 'Suratni qirqish rejimi',
                ],

                'drag_move' => [
                    'label' => 'Suratni ko\'chirish rejimi',
                ],

                'flip_horizontal' => [
                    'label' => 'Suratni yatay o\'zgatirish',
                ],

                'flip_vertical' => [
                    'label' => 'Suratni vertikal o\'zgatirish',
                ],

                'move_down' => [
                    'label' => 'Suratni pastga ko\'chirish',
                ],

                'move_left' => [
                    'label' => 'Suratni chapga ko\'chirish',
                ],

                'move_right' => [
                    'label' => 'Suratni o\'ngga ko\'chirish',
                ],

                'move_up' => [
                    'label' => 'Suratni yuqoriga ko\'chirish',
                ],

                'reset' => [
                    'label' => 'Qayta tiklash',
                ],

                'rotate_left' => [
                    'label' => 'Suratni chapga burish',
                ],

                'rotate_right' => [
                    'label' => 'Suratni o\'ngga burish',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Ko\'rinish nisbiyatini :ratio ga sozlash',
                ],

                'save' => [
                    'label' => 'Saqlash',
                ],

                'zoom_100' => [
                    'label' => 'Suratni 100% ga o\'lchash',
                ],

                'zoom_in' => [
                    'label' => 'Kattalashtirish',
                ],

                'zoom_out' => [
                    'label' => 'Kichraytirish',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Balandlik',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Aylantirish',
                    'unit' => 'daraja',
                ],

                'width' => [
                    'label' => 'Kenglik',
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

                'label' => 'Ko\'rinish nisbiyatlar',

                'no_fixed' => [
                    'label' => 'Ozod',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG fayllarni tahrirlash tavsiya etilmaydi, chunki ulartahrirlanmagan formatlarda saqlanadi. Agar SVG faylni tahrirlashni davom ettirishni xohlaysizmi?',
                    'explanation' => 'SVG fayllar vektor formatda saqlanadi va bu tahrirlanadigan piksel formatlarga nisbatan yuqori sifatli tasvirlarni saqlash imkonini beradi. SVG faylni tahrirlash uchun ilova yoki redaktorlardan foydalanishingiz mumkin.',
                    'proceed' => 'Davom ettirish',
                    'cancel' => 'Bekor qilish',
                ],

            ],

        ],

    ],

];
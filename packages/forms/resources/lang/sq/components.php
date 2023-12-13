<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Kopjo',
            ],

            'add' => [
                'label' => 'Shto në :label',
            ],

            'add_between' => [
                'label' => 'Shto midis blloqeve',
            ],

            'delete' => [
                'label' => 'Fshi',
            ],

            'reorder' => [
                'label' => 'Zhvendos',
            ],

            'move_down' => [
                'label' => 'Zhvendos poshtë',
            ],

            'move_up' => [
                'label' => 'Zhvendos sipër',
            ],

            'collapse' => [
                'label' => 'Mbyll',
            ],

            'expand' => [
                'label' => 'Hap',
            ],

            'collapse_all' => [
                'label' => 'Mbyll të gjitha',
            ],

            'expand_all' => [
                'label' => 'Hap të gjitha',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Çzgjidh të gjitha',
            ],

            'select_all' => [
                'label' => 'Zgjidh të gjitha',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Anulo',
                ],

                'drag_crop' => [
                    'label' => 'Modaliteti i tërheqjes "prerje"',
                ],

                'drag_move' => [
                    'label' => 'Modaliteti i tërheqjes "zhvendos"',
                ],

                'flip_horizontal' => [
                    'label' => 'Kthejeni imazhin horizontal',
                ],

                'flip_vertical' => [
                    'label' => 'Kthejeni imazhin vertikal',
                ],

                'move_down' => [
                    'label' => 'Zhvendos imazhin poshtë',
                ],

                'move_left' => [
                    'label' => 'Zhvendos imazhin majtas',
                ],

                'move_right' => [
                    'label' => 'Zhvendos imazhin djathtas',
                ],

                'move_up' => [
                    'label' => 'Zhvendos imazhin sipër',
                ],

                'reset' => [
                    'label' => 'Rivendos',
                ],

                'rotate_left' => [
                    'label' => 'Rrotulloni imazhin në të majtë',
                ],

                'rotate_right' => [
                    'label' => 'Rrotulloni imazhin në të djathtë',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Cakto raportin e pamjes në :ratio',
                ],

                'save' => [
                    'label' => 'Ruaj',
                ],

                'zoom_100' => [
                    'label' => 'Zmadhoni imazhin në 100%',
                ],

                'zoom_in' => [
                    'label' => 'Zmadhoni',
                ],

                'zoom_out' => [
                    'label' => 'Zvogëloni',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Lartësia',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rrotullimi',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Gjerësia',
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

                'label' => 'Raportet e aspektit',

                'no_fixed' => [
                    'label' => 'i lirë',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Shto rresht',
            ],

            'delete' => [
                'label' => 'Fshi rresht',
            ],

            'reorder' => [
                'label' => 'Renditni përsëri rreshtin',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Çelësi',
            ],

            'value' => [
                'label' => 'Vlera',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Bashkangjit skedarët',
            'blockquote' => 'Blockquote',
            'bold' => 'i theksuar',
            'bullet_list' => 'Lista me pika',
            'code_block' => 'Blloku i kodit',
            'heading' => 'Titull',
            'italic' => 'I pjerrët',
            'link' => 'Link',
            'ordered_list' => 'Lista e numëruar',
            'redo' => 'Ribëj',
            'strike' => 'I mesvijëzuar',
            'table' => 'Tabela',
            'undo' => 'Zhbëj',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Shto në :label',
            ],

            'delete' => [
                'label' => 'Fshi',
            ],

            'clone' => [
                'label' => 'Kopjo',
            ],

            'reorder' => [
                'label' => 'Zhvendos',
            ],

            'move_down' => [
                'label' => 'Zhvendos poshtë',
            ],

            'move_up' => [
                'label' => 'Zhvendos sipër',
            ],

            'collapse' => [
                'label' => 'Mbyll',
            ],

            'expand' => [
                'label' => 'Hap',
            ],

            'collapse_all' => [
                'label' => 'Mbyll të gjitha',
            ],

            'expand_all' => [
                'label' => 'Hap të gjitha',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Link',
                    'unlink' => 'Unlink',
                ],

                'label' => 'URL',

                'placeholder' => 'Fut një URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Bashkangjit skedarët',
            'blockquote' => 'Blockquote',
            'bold' => 'I theksuar',
            'bullet_list' => 'Lista me pika',
            'code_block' => 'Blloku i kodit',
            'h1' => 'Kryetitull',
            'h2' => 'Titull',
            'h3' => 'Nëntitull',
            'italic' => 'I pjerrët',
            'link' => 'Link',
            'ordered_list' => 'Lista e numëruar',
            'redo' => 'Ribëj',
            'strike' => 'I mesvijëzuar',
            'underline' => 'I nënvizuar',
            'undo' => 'Ribëj',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Krijo',

                    'actions' => [

                        'create' => [
                            'label' => 'Krijo',
                        ],

                        'create_another' => [
                            'label' => 'Krijoni dhe krijoni një tjetër',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Modifiko',

                    'actions' => [

                        'save' => [
                            'label' => 'Ruaj',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Po',
            'false' => 'Jo',
        ],

        'loading_message' => 'Po ngarkohet...',

        'max_items_message' => 'Mund të zgjidhet vetëm :count.',

        'no_search_results_message' => 'Asnjë opsion nuk përputhet me kërkimin tuaj.',

        'placeholder' => 'Zgjidhni një opsion',

        'searching_message' => 'Duke kërkuar...',

        'search_prompt' => 'Filloni të shkruani për të kërkuar...',

    ],

    'tags_input' => [
        'placeholder' => 'Tag i ri',
    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Pas',
            ],

            'next_step' => [
                'label' => 'Para',
            ],

        ],

    ],

];

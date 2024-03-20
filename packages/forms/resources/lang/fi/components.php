<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Kloonaa',
            ],

            'add' => [
                'label' => 'Lisää :label',
            ],

            'add_between' => [
                'label' => 'Sijoita',
            ],

            'delete' => [
                'label' => 'Poista',
            ],

            'reorder' => [
                'label' => 'Siirrä',
            ],

            'move_down' => [
                'label' => 'Siirrä alas',
            ],

            'move_up' => [
                'label' => 'Siirrä ylös',
            ],

            'collapse' => [
                'label' => 'Sulje',
            ],

            'expand' => [
                'label' => 'Avaa',
            ],

            'collapse_all' => [
                'label' => 'Sulje kaikki',
            ],

            'expand_all' => [
                'label' => 'Avaa kaikki',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Poista valinta kaikista',
            ],

            'select_all' => [
                'label' => 'Valitse kaikki',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Peruuta',
                ],

                'drag_crop' => [
                    'label' => 'Siirtotila "rajaus"',
                ],

                'drag_move' => [
                    'label' => 'Siirtotila "siirrä"',
                ],

                'flip_horizontal' => [
                    'label' => 'Käännä kuva vaakasuunnassa',
                ],

                'flip_vertical' => [
                    'label' => 'Käännä kuva pystysuunnassa',
                ],

                'move_down' => [
                    'label' => 'Siirrä kuvaa alas',
                ],

                'move_left' => [
                    'label' => 'Siirrä kuvaa vasemmalle ',
                ],

                'move_right' => [
                    'label' => 'Siirrä kuvaa oikealle',
                ],

                'move_up' => [
                    'label' => 'Siirrä kuvaa ylös',
                ],

                'reset' => [
                    'label' => 'Palauta',
                ],

                'rotate_left' => [
                    'label' => 'Käännä kuvaa vasemmalle',
                ],

                'rotate_right' => [
                    'label' => 'Käännä kuvaa oikealle',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Aseta kuvasuhteeksi :ratio',
                ],

                'save' => [
                    'label' => 'Tallenna',
                ],

                'zoom_100' => [
                    'label' => 'Oikea koko',
                ],

                'zoom_in' => [
                    'label' => 'Lähennä',
                ],

                'zoom_out' => [
                    'label' => 'Loitonna',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Korkeus',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Kierre',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Leveys',
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

                'label' => 'Kuvasuhde',

                'no_fixed' => [
                    'label' => 'Vapaa',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG-tiedostojen muokkausta ei suositella, koska laatu voi heikentyä kokoa muuttaessa.\n Oletko varma että haluat jatkaa?',
                    'disabled' => 'SVG-tiedostojen muokkaus on poissa käytöstä koska se voi johtaa laadun heikkenemiseen.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Lisää rivi',
            ],

            'delete' => [
                'label' => 'Poista rivi',
            ],

            'reorder' => [
                'label' => 'Järjestele rivi',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Avain',
            ],

            'value' => [
                'label' => 'Arvo',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Liitä tiedostoja',
            'blockquote' => 'Lainaus',
            'bold' => 'Lihavointi',
            'bullet_list' => 'Lista',
            'code_block' => 'Koodialue',
            'heading' => 'Otsikko',
            'italic' => 'Kursivoitu',
            'link' => 'Linkki',
            'ordered_list' => 'Luettelo',
            'redo' => 'Toista',
            'strike' => 'Yliviivaus',
            'table' => 'Taulukko',
            'undo' => 'Peruuta',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Kyllä',
            'false' => 'Ei',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Lisää :label',
            ],

            'add_between' => [
                'label' => 'Lisää väliin',
            ],

            'delete' => [
                'label' => 'Poista',
            ],

            'clone' => [
                'label' => 'Kloonaa',
            ],

            'reorder' => [
                'label' => 'Siirrä',
            ],

            'move_down' => [
                'label' => 'Siirrä alas',
            ],

            'move_up' => [
                'label' => 'Siirrä ylös',
            ],

            'collapse' => [
                'label' => 'Sulje',
            ],

            'expand' => [
                'label' => 'Avaa',
            ],

            'collapse_all' => [
                'label' => 'Sulje kaikki',
            ],

            'expand_all' => [
                'label' => 'Avaa kaikki',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Linkitä',
                    'unlink' => 'Poista linkki',
                ],

                'label' => 'URL',

                'placeholder' => 'Lisää osoite',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Liitä tiedostoja',
            'blockquote' => 'Lainaus',
            'bold' => 'Lihavointi',
            'bullet_list' => 'Lista',
            'code_block' => 'Koodialue',
            'h1' => 'Pääotsikko',
            'h2' => 'Otsikko',
            'h3' => 'Aliotsikko',
            'italic' => 'Kursivoitu',
            'link' => 'Linkki',
            'ordered_list' => 'Luettelo',
            'redo' => 'Toista',
            'strike' => 'Yliviivaus',
            'underline' => 'Alleviivaus',
            'undo' => 'Peruuta',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Uusi',

                    'actions' => [

                        'create' => [
                            'label' => 'Uusi',
                        ],

                        'create_another' => [
                            'label' => 'Luo & luo toinen',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Muokkaa',

                    'actions' => [

                        'save' => [
                            'label' => 'Tallenna',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Kyllä',
            'false' => 'Ei',
        ],

        'loading_message' => 'Ladataan...',

        'max_items_message' => 'Vain :count voi valita.',

        'no_search_results_message' => 'Haku ei löytänyt tuloksia.',

        'placeholder' => 'Valitse vaihtoehto',

        'searching_message' => 'Haetaan...',

        'search_prompt' => 'Kirjoita hakeaksesi...',

    ],

    'tags_input' => [
        'placeholder' => 'Uusi tunniste',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Piilota salasana',
            ],

            'show_password' => [
                'label' => 'Näytä salasana',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Kyllä',
            'false' => 'Ei',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Edellinen',
            ],

            'next_step' => [
                'label' => 'Seuraava',
            ],

        ],

    ],

];

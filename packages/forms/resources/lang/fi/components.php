<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Kloonaa',
            ],

            'add' => [

                'label' => 'Lisää :label',

                'modal' => [

                    'heading' => 'Lisää :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Lisää',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Lisää lohkon väliin',

                'modal' => [

                    'heading' => 'Lisää :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Lisää',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Poista',
            ],

            'edit' => [

                'label' => 'Muokkaa',

                'modal' => [

                    'heading' => 'Muokkaa lohkoa',

                    'actions' => [

                        'save' => [
                            'label' => 'Tallenna muutokset',
                        ],

                    ],

                ],

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
                'label' => 'Poista valinnat',
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

        'tools' => [
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

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Valitse',

                'actions' => [

                    'select' => [
                        'label' => 'Valitse',
                    ],

                ],

            ],

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

        'actions' => [

            'attach_files' => [

                'label' => 'Lisää tiedosto',

                'modal' => [

                    'heading' => 'Siirrä tiedosto',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Tiedosto',
                                'existing' => 'Korvaa tiedosto',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Vaihtoehtoinen teksti',
                                'existing' => 'Korvaa vaihtoehtoinen teksti',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Sijoita',
                        ],

                        'save' => [
                            'label' => 'Tallenna',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Muokkaa',

                'modal' => [

                    'heading' => 'Linkki',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Avaa uudessa välilehdessä',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Tunnisteita ei löytynyt.',

        'tools' => [
            'align_center' => 'Keskitä',
            'align_end' => 'Tasaa oikealle',
            'align_justify' => 'Tasaa keskelle',
            'align_start' => 'Tasaa vasemmalle',
            'attach_files' => 'Liitä tiedostoja',
            'blockquote' => 'Lainaus',
            'bold' => 'Lihavointi',
            'bullet_list' => 'Lista',
            'clear_formatting' => 'Tyhjennä muotoilu',
            'code' => 'Koodi',
            'code_block' => 'Koodialue',
            'custom_blocks' => 'Lohkot',
            'details' => 'Lisätiedot',
            'h1' => 'Pääotsikko',
            'h2' => 'Otsikko',
            'h3' => 'Aliotsikko',
            'highlight' => 'Korostus',
            'horizontal_rule' => 'Erotin',
            'italic' => 'Kursivoitu',
            'lead' => 'Johdatus',
            'link' => 'Linkki',
            'merge_tags' => 'Yhdistä tunniste',
            'ordered_list' => 'Luettelo',
            'redo' => 'Toista',
            'small' => 'Pieni teksti',
            'strike' => 'Yliviivaus',
            'subscript' => 'Alaindeksi',
            'superscript' => 'Yläindeksi',
            'table' => 'Taulukko',
            'table_delete' => 'Poista taulukko',
            'table_add_column_before' => 'Lisää sarake ennen',
            'table_add_column_after' => 'Lisää sarake jälkeen',
            'table_delete_column' => 'Poista sarake',
            'table_add_row_before' => 'Lisää rivi ennen',
            'table_add_row_after' => 'Lisää rivi jälkeen',
            'table_delete_row' => 'Poista rivi',
            'table_merge_cells' => 'Yhdistä solut',
            'table_split_cell' => 'Pilko solu',
            'table_toggle_header_row' => 'Vaihda otsikkorivi',
            'underline' => 'Alleviivaus',
            'undo' => 'Peruuta',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Uusi',

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

                'label' => 'Muokkaa',

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

        'max_items_message' => 'Voit valita enintään :count.',

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

            'copy' => [
                'label' => 'Kopioi',
                'message' => 'Kopioitu',
            ],

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

];

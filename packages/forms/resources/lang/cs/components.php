<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klonovat',
            ],

            'add' => [

                'label' => 'Přidat k :label',

                'modal' => [

                    'heading' => 'Přidat k :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Přidat',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Vložit',

                'modal' => [

                    'heading' => 'Přidat k :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Přidat',
                        ],

                    ],

                ],

            ],

            'edit' => [

                'label' => 'Upravit',

                'modal' => [

                    'heading' => 'Upravit blok',

                    'actions' => [

                        'save' => [
                            'label' => 'Uložit',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Smazat',
            ],

            'reorder' => [
                'label' => 'Přesunout',
            ],

            'move_down' => [
                'label' => 'Posunout dolů',
            ],

            'move_up' => [
                'label' => 'Posunout nahoru',
            ],

            'collapse' => [
                'label' => 'Skrýt',
            ],

            'expand' => [
                'label' => 'Zobrazit',
            ],

            'collapse_all' => [
                'label' => 'Skrýt vše',
            ],

            'expand_all' => [
                'label' => 'Zobrazit vše',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Odznačit vše',
            ],

            'select_all' => [
                'label' => 'Vybrat vše',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Zrušit',
                ],

                'drag_crop' => [
                    'label' => 'Táhněte pro oříznutí',
                ],

                'drag_move' => [
                    'label' => 'Táhněte pro přesun',
                ],

                'flip_horizontal' => [
                    'label' => 'Překlopit obrázek horizontálně',
                ],

                'flip_vertical' => [
                    'label' => 'Překlopit obrázek vertikálně',
                ],

                'move_down' => [
                    'label' => 'Posunout obrázek dolů',
                ],

                'move_left' => [
                    'label' => 'Posunout obrázek doleva',
                ],

                'move_right' => [
                    'label' => 'Posunout obrázek doprava',
                ],

                'move_up' => [
                    'label' => 'Posunout obrázek nahoru',
                ],

                'reset' => [
                    'label' => 'Reset',
                ],

                'rotate_left' => [
                    'label' => 'Otočit obrázek doleva',
                ],

                'rotate_right' => [
                    'label' => 'Otočit obrázek doprava',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Nastavit poměr stran na :ratio',
                ],

                'save' => [
                    'label' => 'Uložit',
                ],

                'zoom_100' => [
                    'label' => 'Zvětšit obrázek na 100 %',
                ],

                'zoom_in' => [
                    'label' => 'Přiblížit',
                ],

                'zoom_out' => [
                    'label' => 'Oddálit',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Výška',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotace',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Šířka',
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

                'label' => 'Poměr stran',

                'no_fixed' => [
                    'label' => 'Vlastní',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Editace SVG souborů není doporučena, protože může dojít ke ztrátě kvality při škálování.\n Opravdu chcete pokračovat?',
                    'disabled' => 'Úprava souborů SVG je zakázána, protože může vést ke ztrátě kvality při škálování.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Přidat řádek',
            ],

            'delete' => [
                'label' => 'Smazat řádek',
            ],

            'reorder' => [
                'label' => 'Přesunout řádek',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Klíč',
            ],

            'value' => [
                'label' => 'Hodnota',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Přidat soubory',
            'blockquote' => 'Bloková citace',
            'bold' => 'Tučně',
            'bullet_list' => 'Seznam s odrážkami',
            'code_block' => 'Blok kódu',
            'heading' => 'Nadpis',
            'italic' => 'Kurzíva',
            'link' => 'Odkaz',
            'ordered_list' => 'Číslovaný seznam',
            'redo' => 'Vpřed',
            'strike' => 'Přeškrtnutí',
            'table' => 'Tabulka',
            'undo' => 'Zpět',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Vybrat',

                'actions' => [

                    'select' => [
                        'label' => 'Vybrat',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Ano',
            'false' => 'Ne',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Přidat k :label',
            ],

            'add_between' => [
                'label' => 'Vložit mezi',
            ],

            'delete' => [
                'label' => 'Smazat',
            ],

            'clone' => [
                'label' => 'Klonovat',
            ],

            'reorder' => [
                'label' => 'Přesunout',
            ],

            'move_down' => [
                'label' => 'Posunout dolů',
            ],

            'move_up' => [
                'label' => 'Posunout nahoru',
            ],

            'collapse' => [
                'label' => 'Skrýt',
            ],

            'expand' => [
                'label' => 'Zobrazit',
            ],

            'collapse_all' => [
                'label' => 'Skrýt vše',
            ],

            'expand_all' => [
                'label' => 'Zobrazit vše',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Nahrát soubor',

                'modal' => [

                    'heading' => 'Nahrát soubor',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Soubor',
                                'existing' => 'Nahradit soubor',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alternativní text',
                                'existing' => 'Změnit alternativní text',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Vložit',
                        ],

                        'save' => [
                            'label' => 'Uložit',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Upravit',

                'modal' => [

                    'heading' => 'Odkaz',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Otevřít v nové záložce',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Žádné výsledky pro značky slučování.',

        'tools' => [
            'align_center' => 'Zarovnat na střed',
            'align_end' => 'Zarovnat vpravo',
            'align_justify' => 'Zarovnat do bloku',
            'align_start' => 'Zarovnat vlevo',
            'attach_files' => 'Přidat soubory',
            'blockquote' => 'Bloková citace',
            'bold' => 'Tučně',
            'bullet_list' => 'Seznam s odrážkami',
            'clear_formatting' => 'Vymazat formátování',
            'code' => 'Kód',
            'code_block' => 'Blok kódu',
            'custom_blocks' => 'Bloky',
            'details' => 'Detaily',
            'h1' => 'Nadpis 1',
            'h2' => 'Nadpis 2',
            'h3' => 'Nadpis 3',
            'highlight' => 'Zvýraznit',
            'horizontal_rule' => 'Vodorovná čára',
            'italic' => 'Kurzíva',
            'lead' => 'Úvodní text',
            'link' => 'Odkaz',
            'merge_tags' => 'Sloučit značky',
            'ordered_list' => 'Číslovaný seznam',
            'redo' => 'Vpřed',
            'small' => 'Malý text',
            'strike' => 'Přeškrtnutí',
            'subscript' => 'Dolní index',
            'superscript' => 'Horní index',
            'table' => 'Tabulka',
            'table_delete' => 'Smazat tabulku',
            'table_add_column_before' => 'Přidat sloupec před',
            'table_add_column_after' => 'Přidat sloupec za',
            'table_delete_column' => 'Smazat sloupec',
            'table_add_row_before' => 'Přidat řádek nad',
            'table_add_row_after' => 'Přidat řádek pod',
            'table_delete_row' => 'Smazat řádek',
            'table_merge_cells' => 'Sloučit buňky',
            'table_split_cell' => 'Rozdělit buňku',
            'table_toggle_header_row' => 'Přepnout řádek záhlaví',
            'underline' => 'Podtržení',
            'undo' => 'Zpět',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Vytvořit',

                'modal' => [

                    'heading' => 'Vytvořit',

                    'actions' => [

                        'create' => [
                            'label' => 'Vytvořit',
                        ],

                        'create_another' => [
                            'label' => 'Vytvořit a přidat další',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Upravit',

                'modal' => [

                    'heading' => 'Upravit',

                    'actions' => [

                        'save' => [
                            'label' => 'Uložit',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Ano',
            'false' => 'Ne',
        ],

        'loading_message' => 'Načítání...',

        'max_items_message' => 'Lze vybrat pouze 1 položka.|Lze vybrat pouze :count položky.|Lze vybrat pouze :count položek.',

        'no_search_results_message' => 'Vašemu hledání neodpovídají žádné výsledky.',

        'placeholder' => 'Zvolte některou z možností',

        'searching_message' => 'Hledání...',

        'search_prompt' => 'Zadejte hledaný výraz...',

    ],

    'tags_input' => [
        'placeholder' => 'Nový štítek',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Kopírovat',
                'message' => 'Zkopírováno',
            ],

            'hide_password' => [
                'label' => 'Skrýt heslo',
            ],

            'show_password' => [
                'label' => 'Zobrazit heslo',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Ano',
            'false' => 'Ne',
        ],

    ],

];

<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Duplikuj',
            ],

            'add' => [
                'label' => 'Dodaj do :label',
            ],

            'add_between' => [
                'label' => 'Wstaw',
            ],

            'delete' => [
                'label' => 'Usuń',
            ],

            'reorder' => [
                'label' => 'Przesuń',
            ],

            'move_down' => [
                'label' => 'Przesuń w dół',
            ],

            'move_up' => [
                'label' => 'Przesuń w górę',
            ],

            'collapse' => [
                'label' => 'Zwiń',
            ],

            'expand' => [
                'label' => 'Rozwiń',
            ],

            'collapse_all' => [
                'label' => 'Zwiń wszystko',
            ],

            'expand_all' => [
                'label' => 'Rozwiń wszystko',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Odznacz wszystkie',
            ],

            'select_all' => [
                'label' => 'Zaznacz wszystkie',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Anuluj',
                ],

                'drag_crop' => [
                    'label' => 'Tryb przeciągania "przytnij"',
                ],

                'drag_move' => [
                    'label' => 'Tryb przeciągania "przenieś"',
                ],

                'flip_horizontal' => [
                    'label' => 'Odwróć obraz poziomo',
                ],

                'flip_vertical' => [
                    'label' => 'Odwróć obraz pionowp',
                ],

                'move_down' => [
                    'label' => 'Przenieś obraz w dół',
                ],

                'move_left' => [
                    'label' => 'Przenieś obraz w lewo',
                ],

                'move_right' => [
                    'label' => 'Przenieś obraz w prawo',
                ],

                'move_up' => [
                    'label' => 'Przenieś obraz w górę',
                ],

                'reset' => [
                    'label' => 'Zresetuj',
                ],

                'rotate_left' => [
                    'label' => 'Obróć obraz w lewo',
                ],

                'rotate_right' => [
                    'label' => 'Obróć obraz w prawo',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Zmień proporcje na :ratio',
                ],

                'save' => [
                    'label' => 'Zapisz',
                ],

                'zoom_100' => [
                    'label' => 'Przybliż obraz do 100%',
                ],

                'zoom_in' => [
                    'label' => 'Przybliż',
                ],

                'zoom_out' => [
                    'label' => 'Oddal',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Wysokość',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Obrót',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Szerokość',
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

                'label' => 'Proporcje',

                'no_fixed' => [
                    'label' => 'Dowolnie',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Dodaj wiersz',
            ],

            'delete' => [
                'label' => 'Usuń wiersz',
            ],

            'reorder' => [
                'label' => 'Przenieś wiersz',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Klucz',
            ],

            'value' => [
                'label' => 'Wartość',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Dołącz pliki',
            'blockquote' => 'Cytat blokowy',
            'bold' => 'Pogrubienie',
            'bullet_list' => 'Lista punktowana',
            'code_block' => 'Blok kodu',
            'heading' => 'Nagłówek',
            'italic' => 'Kursywa',
            'link' => 'Adres',
            'ordered_list' => 'Lista numerowana',
            'redo' => 'Ponów',
            'strike' => 'Przekreślenie',
            'table' => 'Tabela',
            'undo' => 'Cofnij',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Dodaj do :label',
            ],

            'delete' => [
                'label' => 'Usuń',
            ],

            'clone' => [
                'label' => 'Duplikuj',
            ],

            'reorder' => [
                'label' => 'Przesuń',
            ],

            'move_down' => [
                'label' => 'Przesuń w dół',
            ],

            'move_up' => [
                'label' => 'Przesuń w górę',
            ],

            'collapse' => [
                'label' => 'Zwiń',
            ],

            'expand' => [
                'label' => 'Rozwiń',
            ],

            'collapse_all' => [
                'label' => 'Zwiń wszystko',
            ],

            'expand_all' => [
                'label' => 'Rozwiń wszystko',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Linkuj',
                    'unlink' => 'Usuń link',
                ],

                'label' => 'URL',

                'placeholder' => 'Wprowadź URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Dołącz pliki',
            'blockquote' => 'Cytat',
            'bold' => 'Pogrubienie',
            'bullet_list' => 'Lista punktowana',
            'code_block' => 'Blok kodu',
            'h1' => 'Tytuł',
            'h2' => 'Nagłówek',
            'h3' => 'Podtytuł',
            'italic' => 'Kursywa',
            'link' => 'Adres',
            'ordered_list' => 'Lista numerowana',
            'redo' => 'Ponów',
            'strike' => 'Przekreślenie',
            'underline' => 'Podkreślenie',
            'undo' => 'Cofnij',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Utwórz',

                    'actions' => [

                        'create' => [
                            'label' => 'Utwórz',
                        ],

                        'create_another' => [
                            'label' => 'Utwórz i utwórz kolejny',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Edytuj',

                    'actions' => [

                        'save' => [
                            'label' => 'Zapisz',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Tak',
            'false' => 'Nie',
        ],

        'loading_message' => 'Wczytywanie...',

        'max_items_message' => 'Można wybrać tylko :count elementów.',

        'no_search_results_message' => 'Żadne wyniki nie pasują do Twojego wyszukiwania.',

        'placeholder' => 'Wybierz z listy',

        'searching_message' => 'Szukanie...',

        'search_prompt' => 'Zacznij pisać aby wyszukać...',

    ],

    'tags_input' => [
        'placeholder' => 'Nowy tag',
    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Poprzedni',
            ],

            'next_step' => [
                'label' => 'Następny',
            ],

        ],

    ],

];

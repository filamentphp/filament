<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Κλωνοποίηση',
            ],

            'add' => [

                'label' => 'Προσθήκη στο :label',

                'modal' => [

                    'heading' => 'Προσθήκη στο :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Προσθήκη',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Εισαγωγή ανάμεσα στα μπλοκ',

                'modal' => [

                    'heading' => 'Προσθήκη στο :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Προσθήκη',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Διαγραφή',
            ],

            'edit' => [

                'label' => 'Επεξεργασία',

                'modal' => [

                    'heading' => 'Επεξεργασία μπλοκ',

                    'actions' => [

                        'save' => [
                            'label' => 'Αποθήκευση αλλαγών',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Μετακίνηση',
            ],

            'move_down' => [
                'label' => 'Μετακίνηση προς τα κάτω',
            ],

            'move_up' => [
                'label' => 'Μετακίνηση προς τα πάνω',
            ],

            'collapse' => [
                'label' => 'Σύμπτυξη',
            ],

            'expand' => [
                'label' => 'Ανάπτυξη',
            ],

            'collapse_all' => [
                'label' => 'Σύμπτυξη όλων',
            ],

            'expand_all' => [
                'label' => 'Ανάπτυξη όλων',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Αποεπιλογή όλων',
            ],

            'select_all' => [
                'label' => 'Επιλογή όλων',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Ακύρωση',
                ],

                'drag_crop' => [
                    'label' => 'Λειτουργία συρσίματος "περικοπή"',
                ],

                'drag_move' => [
                    'label' => 'Λειτουργία συρσίματος "μετακίνηση"',
                ],

                'flip_horizontal' => [
                    'label' => 'Αντιστροφή εικόνας οριζόντια',
                ],

                'flip_vertical' => [
                    'label' => 'Αντιστροφή εικόνας κάθετα',
                ],

                'move_down' => [
                    'label' => 'Μετακίνηση εικόνας προς τα κάτω',
                ],

                'move_left' => [
                    'label' => 'Μετακίνηση εικόνας προς τα αριστερά',
                ],

                'move_right' => [
                    'label' => 'Μετακίνηση εικόνας προς τα δεξιά',
                ],

                'move_up' => [
                    'label' => 'Μετακίνηση εικόνας προς τα πάνω',
                ],

                'reset' => [
                    'label' => 'Επαναφορά',
                ],

                'rotate_left' => [
                    'label' => 'Περιστροφή εικόνας προς τα αριστερά',
                ],

                'rotate_right' => [
                    'label' => 'Περιστροφή εικόνας προς τα δεξιά',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Ορισμός αναλογίας διαστάσεων σε :ratio',
                ],

                'save' => [
                    'label' => 'Αποθήκευση',
                ],

                'zoom_100' => [
                    'label' => 'Μεγέθυνση εικόνας στο 100%',
                ],

                'zoom_in' => [
                    'label' => 'Μεγέθυνση',
                ],

                'zoom_out' => [
                    'label' => 'Σμίκρυνση',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Ύψος',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Περιστροφή',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Πλάτος',
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

                'label' => 'Αναλογίες διαστάσεων',

                'no_fixed' => [
                    'label' => 'Ελεύθερο',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Η επεξεργασία αρχείων SVG δεν συνιστάται, καθώς μπορεί να οδηγήσει σε απώλεια ποιότητας κατά την κλιμάκωση.\n Είστε σίγουρος/η ότι θέλετε να συνεχίσετε;',
                    'disabled' => 'Η επεξεργασία αρχείων SVG είναι απενεργοποιημένη, καθώς μπορεί να οδηγήσει σε απώλεια ποιότητας κατά την κλιμάκωση.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Προσθήκη γραμμής',
            ],

            'delete' => [
                'label' => 'Διαγραφή γραμμής',
            ],

            'reorder' => [
                'label' => 'Αναδιάταξη γραμμής',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Κλειδί',
            ],

            'value' => [
                'label' => 'Τιμή',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Επισύναψη αρχείων',
            'blockquote' => 'Blockquote',
            'bold' => 'Έντονα',
            'bullet_list' => 'Λίστα με κουκκίδες',
            'code_block' => 'Μπλοκ κώδικα',
            'heading' => 'Επικεφαλίδα',
            'italic' => 'Πλάγια',
            'link' => 'Σύνδεσμος',
            'ordered_list' => 'Λίστα με αριθμούς',
            'redo' => 'Επαναφορά',
            'strike' => 'Διακριτή διαγραφή',
            'table' => 'Πίνακας',
            'undo' => 'Αναίρεση',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Ναι',
            'false' => 'Όχι',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Προσθήκη στο :label',
            ],

            'add_between' => [
                'label' => 'Εισαγωγή μεταξύ',
            ],

            'delete' => [
                'label' => 'Διαγραφή',
            ],

            'clone' => [
                'label' => 'Κλωνοποίηση',
            ],

            'reorder' => [
                'label' => 'Μετακίνηση',
            ],

            'move_down' => [
                'label' => 'Μετακίνηση προς τα κάτω',
            ],

            'move_up' => [
                'label' => 'Μετακίνηση προς τα πάνω',
            ],

            'collapse' => [
                'label' => 'Σύμπτυξη',
            ],

            'expand' => [
                'label' => 'Ανάπτυξη',
            ],

            'collapse_all' => [
                'label' => 'Σύμπτυξη όλων',
            ],

            'expand_all' => [
                'label' => 'Ανάπτυξη όλων',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Σύνδεσμος',
                    'unlink' => 'Αφαίρεση συνδέσμου',
                ],

                'label' => 'URL',

                'placeholder' => 'Εισάγετε ένα URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Επισύναψη αρχείων',
            'blockquote' => 'Blockquote',
            'bold' => 'Έντονα',
            'bullet_list' => 'Λίστα με κουκκίδες',
            'code_block' => 'Μπλοκ κώδικα',
            'h1' => 'Τίτλος',
            'h2' => 'Επικεφαλίδα',
            'h3' => 'Υπότιτλος',
            'italic' => 'Πλάγια',
            'link' => 'Σύνδεσμος',
            'ordered_list' => 'Λίστα με αριθμούς',
            'redo' => 'Επαναφορά',
            'strike' => 'Διακριτή διαγραφή',
            'underline' => 'Υπογράμμιση',
            'undo' => 'Αναίρεση',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Δημιουργία',

                'modal' => [

                    'heading' => 'Δημιουργία',

                    'actions' => [

                        'create' => [
                            'label' => 'Δημιουργία',
                        ],

                        'create_another' => [
                            'label' => 'Δημιουργία & δημιουργία άλλου',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Επεξεργασία',

                'modal' => [

                    'heading' => 'Επεξεργασία',

                    'actions' => [

                        'save' => [
                            'label' => 'Αποθήκευση',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Ναι',
            'false' => 'Όχι',
        ],

        'loading_message' => 'Φόρτωση...',

        'max_items_message' => 'Μόνο :count μπορούν να επιλεχθούν.',

        'no_search_results_message' => 'Δεν υπάρχουν επιλογές που να ταιριάζουν με την αναζήτησή σας.',

        'placeholder' => 'Επιλέξτε μια επιλογή',

        'searching_message' => 'Αναζήτηση...',

        'search_prompt' => 'Ξεκινήστε να πληκτρολογείτε για αναζήτηση...',

    ],

    'tags_input' => [
        'placeholder' => 'Νέα ετικέτα',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Απόκρυψη κωδικού πρόσβασης',
            ],

            'show_password' => [
                'label' => 'Εμφάνιση κωδικού πρόσβασης',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Ναι',
            'false' => 'Όχι',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Πίσω',
            ],

            'next_step' => [
                'label' => 'Επόμενο',
            ],

        ],

    ],

];

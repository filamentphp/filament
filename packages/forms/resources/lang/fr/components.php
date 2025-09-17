<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Cloner',
            ],

            'add' => [

                'label' => 'Ajouter à :label',

                'modal' => [

                    'heading' => 'Ajouter à :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Ajouter',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Insérer entre les blocs',

                'modal' => [

                    'heading' => 'Ajouter à :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Ajouter',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Supprimer',
            ],

            'edit' => [

                'label' => 'Modifier',

                'modal' => [

                    'heading' => 'Modifier le bloc',

                    'actions' => [

                        'save' => [
                            'label' => 'Sauvegarder les modifications',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Déplacer',
            ],

            'move_down' => [
                'label' => 'Déplacer vers le bas',
            ],

            'move_up' => [
                'label' => 'Déplacer vers le haut',
            ],

            'collapse' => [
                'label' => 'Plier',
            ],

            'expand' => [
                'label' => 'Déplier',
            ],

            'collapse_all' => [
                'label' => 'Tout plier',
            ],

            'expand_all' => [
                'label' => 'Tout déplier',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Désélectionner tout',
            ],

            'select_all' => [
                'label' => 'Sélectionner tout',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Annuler',
                ],

                'drag_crop' => [
                    'label' => 'Mode glisser "recadrer"',
                ],

                'drag_move' => [
                    'label' => 'Mode glisser "déplacer"',
                ],

                'flip_horizontal' => [
                    'label' => "Retourner l'image horizontalement",
                ],

                'flip_vertical' => [
                    'label' => "Retourner l'image verticalement",
                ],

                'move_down' => [
                    'label' => "Déplacer l'image vers le bas",
                ],

                'move_left' => [
                    'label' => "Déplacer l'image vers la gauche",
                ],

                'move_right' => [
                    'label' => "Déplacer l'image vers la droite",
                ],

                'move_up' => [
                    'label' => "Déplacer l'image vers le haut",
                ],

                'reset' => [
                    'label' => 'Réinitialiser',
                ],

                'rotate_left' => [
                    'label' => "Faire pivoter l'image vers la gauche",
                ],

                'rotate_right' => [
                    'label' => "Faire pivoter l'image vers la droite",
                ],

                'set_aspect_ratio' => [
                    'label' => 'Régler les proportions sur :ratio',
                ],

                'save' => [
                    'label' => 'Sauvegarder',
                ],

                'zoom_100' => [
                    'label' => "Agrandir l'image à 100%",
                ],

                'zoom_in' => [
                    'label' => 'Zoomer',
                ],

                'zoom_out' => [
                    'label' => 'Dézoomer',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Hauteur',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotation',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Largeur',
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

                'label' => 'Proportions',

                'no_fixed' => [
                    'label' => 'Libre',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'La modification des fichiers SVG n\'est pas recommandée car elle peut entraîner une perte de qualité lors de la mise à l\'échelle.\n Êtes-vous sûr de vouloir continuer ?',
                    'disabled' => 'La modification des fichiers SVG est désactivée car elle peut entraîner une perte de qualité lors de la mise à l\'échelle.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Ajouter une ligne',
            ],

            'delete' => [
                'label' => 'Supprimer une ligne',
            ],

            'reorder' => [
                'label' => 'Réorganiser une ligne',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Clé',
            ],

            'value' => [
                'label' => 'Valeur',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Joindre des fichiers',
            'blockquote' => 'Citation',
            'bold' => 'Gras',
            'bullet_list' => 'Liste à puces',
            'code_block' => 'Bloc de code',
            'heading' => 'Titre',
            'italic' => 'Italique',
            'link' => 'Lien',
            'ordered_list' => 'Liste numérotée',
            'redo' => 'Refaire',
            'strike' => 'Barré',
            'table' => 'Table',
            'undo' => 'Annuler',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Sélectionner',

                'actions' => [

                    'select' => [
                        'label' => 'Sélectionner',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Oui',
            'false' => 'Non',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Ajouter à :label',
            ],

            'add_between' => [
                'label' => 'Insérer entre',
            ],

            'delete' => [
                'label' => 'Supprimer',
            ],

            'clone' => [
                'label' => 'Cloner',
            ],

            'reorder' => [
                'label' => 'Déplacer',
            ],

            'move_down' => [
                'label' => 'Déplacer vers le bas',
            ],

            'move_up' => [
                'label' => 'Déplacer vers le haut',
            ],

            'collapse' => [
                'label' => 'Plier',
            ],

            'expand' => [
                'label' => 'Déplier',
            ],

            'collapse_all' => [
                'label' => 'Tout plier',
            ],

            'expand_all' => [
                'label' => 'Tout déplier',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Téléverser un fichier',

                'modal' => [

                    'heading' => 'Téléverser un fichier',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Fichier',
                                'existing' => 'Remplacer le fichier',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Texte alternatif',
                                'existing' => 'Modifier le texte alternatif',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Insérer',
                        ],

                        'save' => [
                            'label' => 'Sauvegarder',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Modifier',

                'modal' => [

                    'heading' => 'Lien',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Ouvrir dans un nouvel onglet',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Aucun résultat de balise de fusion.',

        'tools' => [
            'align_center' => 'Centrer',
            'align_end' => 'Aligner à la fin',
            'align_justify' => 'Justifier',
            'align_start' => 'Aligner au début',
            'attach_files' => 'Joindre des fichiers',
            'blockquote' => 'Citation',
            'bold' => 'Gras',
            'bullet_list' => 'Liste à puces',
            'clear_formatting' => 'Effacer la mise en forme',
            'code_block' => 'Bloc de code',
            'custom_blocks' => 'Blocs',
            'details' => 'Détails',
            'h1' => 'Titre',
            'h2' => 'Titre',
            'h3' => 'Sous-titre',
            'highlight' => 'Surligner',
            'horizontal_rule' => 'Ligne horizontale',
            'italic' => 'Italique',
            'lead' => 'Texte principal',
            'link' => 'Lien',
            'merge_tags' => 'Balises de fusion',
            'ordered_list' => 'Liste numérotée',
            'redo' => 'Refaire',
            'small' => 'Petit texte',
            'strike' => 'Barré',
            'subscript' => 'Indice',
            'superscript' => 'Exposant',
            'table' => 'Tableau',
            'table_delete' => 'Supprimer le tableau',
            'table_add_column_before' => 'Ajouter une colonne avant',
            'table_add_column_after' => 'Ajouter une colonne après',
            'table_delete_column' => 'Supprimer la colonne',
            'table_add_row_before' => 'Ajouter une ligne au-dessus',
            'table_add_row_after' => 'Ajouter une ligne en dessous',
            'table_delete_row' => 'Supprimer la ligne',
            'table_merge_cells' => 'Fusionner les cellules',
            'table_split_cell' => 'Diviser la cellule',
            'table_toggle_header_row' => 'Basculer la ligne d\'en-tête',
            'underline' => 'Souligné',
            'undo' => 'Annuler',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Créer',

                'modal' => [

                    'heading' => 'Créer',

                    'actions' => [

                        'create' => [
                            'label' => 'Créer',
                        ],

                        'create_another' => [
                            'label' => 'Créer & créer un autre',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Modifier',

                'modal' => [

                    'heading' => 'Modifier',

                    'actions' => [

                        'save' => [
                            'label' => 'Sauvegarder',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Oui',
            'false' => 'Non',
        ],

        'loading_message' => 'En chargement...',

        'max_items_message' => 'Uniquement :count peuvent être sélectionnés.',

        'no_search_results_message' => 'Aucune option ne correspond à votre recherche.',

        'placeholder' => 'Sélectionnez une option',

        'searching_message' => 'En recherche...',

        'search_prompt' => 'Commencez à taper pour rechercher...',

    ],

    'tags_input' => [
        'placeholder' => 'Nouveau tag',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Masquer le mot de passe',
            ],

            'show_password' => [
                'label' => 'Montrer le mot de passe',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Oui',
            'false' => 'Non',
        ],

    ],

];

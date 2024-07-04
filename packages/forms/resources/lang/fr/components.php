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
                'label' => 'Ajouter entre',
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
                    'heading' => 'Modifier',
                    'actions' => [
                        'save' => [
                            'label' => 'Sauvegarder',
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

        'toolbar_buttons' => [
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

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Lien',
                    'unlink' => 'Dissocier',
                ],

                'label' => 'URL',

                'placeholder' => 'Entrez une URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Joindre fichiers',
            'blockquote' => 'Citation',
            'bold' => 'Gras',
            'bullet_list' => 'Liste à puces',
            'code_block' => 'Code',
            'h1' => 'Titre',
            'h2' => 'Titre',
            'h3' => 'Sous-titre',
            'italic' => 'Italique',
            'link' => 'Lien',
            'ordered_list' => 'Liste numérotée',
            'redo' => 'Refaire',
            'strike' => 'Barré',
            'underline' => 'Souligné',
            'undo' => 'Annuler',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Créer',

                    'actions' => [

                        'create' => [
                            'label' => 'Créer',
                        ],

                        'create_another' => [
                            'label' => 'Créer & Ajouter un autre',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

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

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Précédent',
            ],

            'next_step' => [
                'label' => 'Suivant',
            ],

        ],

    ],

];

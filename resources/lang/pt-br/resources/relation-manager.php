<?php

return [

    'buttons' => [

        'attach' => [
            'label' => 'Vincular existente',
        ],

        'create' => [
            'label' => 'Novo',
        ],

        'detach' => [
            'label' => 'Desvincular selecionado',
        ],

    ],

    'modals' => [

        'attach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancelar',
                ],

                'attach' => [
                    'label' => 'Vincular',
                ],

                'attachAnother' => [
                    'label' => 'Vincular & Vincular outro',
                ],

            ],

            'form' => [

                'related' => [
                    'placeholder' => 'Comece a digitar para pesquisar...',
                ],

            ],

            'heading' => 'Vincular existente',

            'messages' => [
                'attached' => 'Vinculado!',
            ],

        ],

        'create' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancelar',
                ],

                'create' => [
                    'label' => 'Criar',
                ],

                'createAnother' => [
                    'label' => 'Criar & Criar outro',
                ],

            ],

            'heading' => 'Criar',

            'messages' => [
                'created' => 'Criado!',
            ],

        ],

        'detach' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancelar',
                ],

                'detach' => [
                    'label' => 'Desvincular selecionado',
                ],

            ],

            'description' => 'Tem certeza de que deseja desvincular os registros selecionados? Essa ação não pode ser desfeita.',

            'heading' => 'Desvincular os registros selecionados? ',

            'messages' => [
                'detached' => 'Desvinculado!',
            ],

        ],

        'edit' => [

            'buttons' => [

                'cancel' => [
                    'label' => 'Cancelar',
                ],

                'save' => [
                    'label' => 'Salvar',
                ],

            ],

            'heading' => 'Editar',

            'messages' => [
                'saved' => 'Salvo!',
            ],

        ],

    ],

];

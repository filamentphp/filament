<?php

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Pular para o conteúdo',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Gerenciar assinatura',
        ],

        'logout' => [
            'label' => 'Sair',
        ],

        'open_database_notifications' => [
            'label' => 'Abrir notificações',
            'label_with_unread_count' => '{1} Notificações, :count notificação não lida|[2,*] Notificações, :count notificações não lidas',
        ],

        'open_user_menu' => [
            'label' => 'Menu do usuário',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Recolher barra lateral',
            ],

            'expand' => [
                'label' => 'Expandir barra lateral',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Tema',

            'dark' => [
                'label' => 'Mudar para tema escuro',
            ],

            'light' => [
                'label' => 'Mudar para tema claro',
            ],

            'system' => [
                'label' => 'Mudar para tema do sistema',
            ],

        ],

    ],

    'navigation' => [
        'label' => 'Navegação da barra lateral',
    ],

    'topbar' => [
        'label' => 'Barra superior',
    ],

    'avatar' => [
        'alt' => 'Avatar de :name',
    ],

    'logo' => [
        'alt' => 'Logotipo de :name',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Buscar locatário',
            'placeholder' => 'Buscar',
        ],

    ],

];

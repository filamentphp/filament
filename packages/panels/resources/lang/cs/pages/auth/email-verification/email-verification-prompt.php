<?php

return [

    'title' => 'Ověřte svou e-mailovou adresu',

    'heading' => 'Ověřte svou e-mailovou adresu',

    'actions' => [

        'resend_notification' => [
            'label' => 'Znovu odeslat ověřovací e-mail',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Neobdrželi jste e-mail, který jsme poslali?',
        'notification_sent' => 'Na vaši e-mailovou adresu :email jsme zaslali zprávu s pokyny pro ověření této adresy.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Ověřovací e-mail byl odeslán',
        ],

        'notification_resend_throttled' => [
            'title' => 'Příliš mnoho požadavků',
            'body' => 'Zkuste to prosím znovu za :seconds sekund.',
        ],

    ],

];

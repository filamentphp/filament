<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'Adresse Email',
        ],

        'name' => [
            'label' => 'Nom',
        ],

        'password' => [
            'label' => 'Nouveau mot de passe',
            'validation_attribute' => 'mot de passe',
        ],

        'password_confirmation' => [
            'label' => 'Confirmer le nouveau mot de passe',
            'validation_attribute' => 'confirmation du nouveau mot de passe',
        ],

        'current_password' => [
            'label' => 'Mot de passe actuel',
            'below_content' => 'Pour des raisons de sécurité, veuillez confirmer votre mot de passe pour continuer.',
            'validation_attribute' => 'mot de passe actuel',
        ],

        'actions' => [

            'save' => [
                'label' => 'Sauvegarder les modifications',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Authentification à deux facteurs (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Demande de modification de l\'adresse email envoyée',
            'body' => 'Une demande de modification de votre adresse email a été envoyée à :email. Veuillez vérifier votre email pour vérifier la modification.',
        ],

        'saved' => [
            'title' => 'Sauvegardé',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Annuler',
        ],

    ],

];

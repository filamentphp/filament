<?php

return [

    'buttons' => [

        'submit' => [
            'label' => 'Envoyer le lien de réinitialisation du mot de passe',
        ],

    ],

    'form' => [

        'email' => [
            'hint' => 'Retour a la connexion',
            'label' => 'Adresse courriel',
        ],

    ],

    'messages' => [

        'throttled' => 'Trop de tentatives de connexion. Veuillez réessayer dans :seconds secondes.',

        'passwords' => [
            'sent' => 'Nous avons envoyé votre lien de réinitialisation de mot de passe par courriel!',
            'throttled' => 'Veuillez patienter avant de réessayer.',
            'user' => 'Nous ne pouvons pas trouver un utilisateur avec cette adresse courriel.',
        ],

    ],

    'title' => 'Réinitialiser le mot de passe',

];

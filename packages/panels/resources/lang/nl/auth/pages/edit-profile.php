<?php

return [

    'label' => 'Profiel',

    'form' => [

        'email' => [
            'label' => 'E-mailadres',
        ],

        'name' => [
            'label' => 'Naam',
        ],

        'password' => [
            'label' => 'Nieuw wachtwoord',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Bevestig nieuw wachtwoord',
            'validation_attribute' => 'password confirmation',
        ],

        'current_password' => [
            'label' => 'Huidig wachtwoord',
            'below_content' => 'Voor de veiligheid, bevestig uw wachtwoord om door te gaan.',
            'validation_attribute' => 'current password',
        ],

        'actions' => [

            'save' => [
                'label' => 'Opslaan',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Twee-factor-authenticatie (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'E-mailadres wijzigingsverzoek verzonden',
            'body' => 'Er is een verzoek om uw e-mailadres te wijzigen verzonden naar :email. Controleer uw e-mail om de wijziging te verifiëren.',
        ],

        'saved' => [
            'title' => 'Opgeslagen',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Terug',
        ],

    ],

];

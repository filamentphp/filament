<?php

return [

    'label' => 'පිහිටුවන්න',

    'modal' => [

        'heading' => 'සත්‍යාපන යෙදුම පිහිටුවන්න',

        'description' => <<<'BLADE'
            මෙම ක්‍රියාවලිය සම්පූර්ණ කිරීමට ඔබට Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) වැනි යෙදුමක් අවශ්‍ය වේ.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'ඔබගේ සත්‍යාපන යෙදුමෙන් මෙම QR කේතය ස්කෑන් කරන්න:',

                'alt' => 'සත්‍යාපන යෙදුමකින් ස්කෑන් කිරීමට QR කේතය',

            ],

            'text_code' => [

                'instruction' => 'නැතහොත් මෙම කේතය අතින් ඇතුළත් කරන්න:',

                'messages' => [
                    'copied' => 'පිටපත් කරන ලදී',
                ],

            ],

            'recovery_codes' => [
                'instruction' => 'කරුණාකර පහත ප්‍රතිසාධන කේත ආරක්ෂිත ස්ථානයක සුරකින්න. ඒවා පෙන්වන්නේ එක් වරක් පමණි, නමුත් ඔබගේ සත්‍යාපන යෙදුමට ප්‍රවේශය අහිමි වුවහොත් ඒවා ඔබට අවශ්‍ය වනු ඇත:',
            ],

        ],

        'form' => [

            'password' => [
                'label' => 'වත්මන් මුරපදය',
                'validation_attribute' => 'වත්මන් මුරපදය',
            ],

            'code' => [

                'label' => 'සත්‍යාපන යෙදුමේ ඇති ඉලක්කම් 6 කින් යුත් කේතය ඇතුළත් කරන්න',

                'validation_attribute' => 'කේතය',

                'below_content' => 'ඔබ සෑම වතාවකම පිවිසෙන විට හෝ සංවේදී ක්‍රියා සිදු කරන විට ඔබගේ සත්‍යාපන යෙදුමේ ඇති ඉලක්කම් 6 කින් යුත් කේතය ඇතුළත් කළ යුතුය.',

                'messages' => [
                    'invalid' => 'ඔබ ඇතුළත් කළ කේතය වලංගු නැත.',
                    'rate_limited' => 'උත්සාහයන් වැඩියි. කරුණාකර පසුව නැවත උත්සාහ කරන්න.',
                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'සත්‍යාපන යෙදුම සක්‍රිය කරන්න',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'සත්‍යාපන යෙදුම සක්‍රිය කර ඇත',
        ],

    ],

];

<?php

return [

    'label' => 'সেট আপ করুন',

    'modal' => [

        'heading' => 'অথেনটিকেটর অ্যাপ সেট আপ করুন',

        'description' => <<<'BLADE'
            এই প্রক্রিয়াটি সম্পন্ন করতে আপনার গুগল অথেনটিকেটর (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) এর মত একটি অ্যাপ প্রয়োজন হবে।
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'আপনার অথেনটিকেটর অ্যাপ দিয়ে এই QR কোডটি স্ক্যান করুন:',

                'alt' => 'অথেনটিকেটর অ্যাপ দিয়ে স্ক্যান করার জন্য QR কোড',

            ],

            'text_code' => [

                'instruction' => 'অথবা এই কোডটি ম্যানুয়ালি প্রবেশ করান:',

                'messages' => [
                    'copied' => 'কপি হয়েছে',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'অনুগ্রহ করে নিচের রিকভারি কোডগুলো একটি নিরাপদ স্থানে সংরক্ষণ করুন। এগুলো শুধুমাত্র একবার দেখানো হবে, তবে আপনার অথেনটিকেটর অ্যাপের অ্যাক্সেস হারিয়ে ফেললে এগুলো প্রয়োজন হবে:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'অথেনটিকেটর অ্যাপ থেকে ৬-ডিজিটের কোডটি প্রবেশ করান',

                'validation_attribute' => 'কোড',

                'below_content' => 'প্রতিবার সাইন ইন করার সময় বা সংবেদনশীল কাজ করার সময় আপনাকে আপনার অথেনটিকেটর অ্যাপ থেকে ৬-ডিজিটের কোডটি প্রবেশ করাতে হবে।',

                'messages' => [

                    'invalid' => 'আপনার প্রবেশ করানো কোডটি সঠিক নয়।',

                    'rate_limited' => 'অত্যধিক চেষ্টা। অনুগ্রহ করে পরে আবার চেষ্টা করুন।',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'অথেনটিকেটর অ্যাপ সক্রিয় করুন',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'অথেনটিকেটর অ্যাপ সক্রিয় করা হয়েছে',
        ],

    ],

];

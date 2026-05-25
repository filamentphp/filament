<?php

return [

    'single' => [

        'label' => 'পুনরুদ্ধার করুন',

        'modal' => [

            'heading' => ':label পুনরুদ্ধার করুন',

            'actions' => [

                'restore' => [
                    'label' => 'পুনরুদ্ধার করুন',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'পুনরুদ্ধার করা হয়েছে',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'নির্বাচিতগুলো পুনরুদ্ধার করুন',

        'modal' => [

            'heading' => 'নির্বাচিত :label পুনরুদ্ধার করুন',

            'actions' => [

                'restore' => [
                    'label' => 'পুনরুদ্ধার করুন',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'পুনরুদ্ধার করা হয়েছে',
            ],

            'restored_partial' => [
                'title' => ':total এর মধ্যে :countটি পুনরুদ্ধার করা হয়েছে',
                'missing_authorization_failure_message' => 'আপনার :countটি পুনরুদ্ধার করার অনুমতি নেই।',
                'missing_processing_failure_message' => ':countটি পুনরুদ্ধার করা সম্ভব হয়নি।',
            ],

            'restored_none' => [
                'title' => 'পুনরুদ্ধার করতে ব্যর্থ হয়েছে',
                'missing_authorization_failure_message' => 'আপনার :countটি পুনরুদ্ধার করার অনুমতি নেই।',
                'missing_processing_failure_message' => ':countটি পুনরুদ্ধার করা সম্ভব হয়নি।',
            ],

        ],

    ],

];

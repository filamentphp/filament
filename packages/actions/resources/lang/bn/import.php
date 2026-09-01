<?php

return [

    'label' => ':label আমদানি করুন',

    'modal' => [

        'heading' => ':label আমদানি করুন',

        'form' => [

            'file' => [

                'label' => 'ফাইল',

                'placeholder' => 'একটি CSV ফাইল আপলোড করুন',

                'rules' => [
                    'duplicate_columns' => '{0} ফাইলে একাধিক খালি কলাম হেডার থাকতে হবে না।|{1,*} ফাইলে ডুপ্লিকেট কলাম হেডার থাকতে হবে না: :columns।',
                ],

            ],

            'columns' => [
                'label' => 'কলাম',
                'placeholder' => 'একটি কলাম নির্বাচন করুন',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'উদাহরণ CSV ফাইল ডাউনলোড করুন',
            ],

            'import' => [
                'label' => 'আমদানি করুন',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'আমদানি সম্পন্ন হয়েছে',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'ব্যর্থ সারি সম্পর্কে তথ্য ডাউনলোড করুন|ব্যর্থ সারিগুলি সম্পর্কে তথ্য ডাউনলোড করুন',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'আপলোড করা CSV ফাইল খুব বড়',
            'body' => 'আপনি একবারে ১টি সারির বেশি আমদানি করতে পারবেন না।|আপনি একবারে :countটি সারির বেশি আমদানি করতে পারবেন না।',
        ],

        'started' => [
            'title' => 'আমদানি শুরু হয়েছে',
            'body' => 'আপনার আমদানি শুরু হয়েছে এবং ১টি সারি ব্যাকগ্রাউন্ডে প্রক্রিয়াকরণ করা হবে।|আপনার আমদানি শুরু হয়েছে এবং :countটি সারি ব্যাকগ্রাউন্ডে প্রক্রিয়াকরণ করা হবে।',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'ত্রুটি',
        'system_error' => 'সিস্টেম ত্রুটি, দয়া করে সহায়তা নিন।',
        'column_mapping_required_for_new_record' => ':attribute কলাম ফাইলের একটি কলামের সাথে ম্যাপ করা হয়নি, কিন্তু নতুন রেকর্ড তৈরি করার জন্য এটি প্রয়োজন।',
    ],

];

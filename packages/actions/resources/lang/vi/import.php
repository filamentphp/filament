<?php

return [

    'label' => 'Nhập :label',

    'modal' => [

        'heading' => 'Nhập :label',

        'form' => [

            'file' => [

                'label' => 'Tệp',

                'placeholder' => 'Tải lên tệp CSV',

                'rules' => [
                    'duplicate_columns' => '{0} Tệp không được chứa nhiều hơn một tiêu đề cột trống.|{1,*} Tệp không được chứa các tiêu đề cột trùng lặp: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Cột',
                'placeholder' => 'Chọn một cột',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Tải xuống tệp CSV mẫu',
            ],

            'import' => [
                'label' => 'Nhập',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Quá trình nhập hoàn tất',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Tải xuống thông tin về hàng bị lỗi|Tải xuống thông tin về những hàng bị lỗi',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Tệp CSV đã tải lên quá lớn',
            'body' => 'Bạn không thể nhập nhiều hơn 1 hàng cùng một lúc.|Bạn không thể nhập nhiều hơn :count hàng cùng một lúc.',
        ],

        'started' => [
            'title' => 'Quá trình nhập đã bắt đầu',
            'body' => 'Quá trình nhập của bạn đã bắt đầu và sẽ xử lý 1 hàng trong nền.|Quá trình nhập của bạn đã bắt đầu và sẽ xử lý :count hàng trong nền.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'lỗi',
        'system_error' => 'Lỗi hệ thống, vui lòng liên hệ bộ phận hỗ trợ.',
        'column_mapping_required_for_new_record' => 'Cột :attribute không được ánh xạ với cột nào trong tệp, nhưng cột này là bắt buộc để tạo bản ghi mới.',
    ],

];

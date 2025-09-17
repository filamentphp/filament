<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => '복제',
            ],

            'add' => [

                'label' => ':label 추가',

                'modal' => [

                    'heading' => ':label 추가',

                    'actions' => [

                        'add' => [
                            'label' => '추가',
                        ],

                    ],

                ],
            ],
            'add_between' => [

                'label' => '블록 사이에 삽입',

                'modal' => [

                    'heading' => ':label 추가',

                    'actions' => [

                        'add' => [
                            'label' => '추가',
                        ],

                    ],

                ],
            ],
            'delete' => [
                'label' => '삭제',
            ],

            'edit' => [

                'label' => '수정',

                'modal' => [

                    'heading' => '블록 수정',

                    'actions' => [

                        'save' => [
                            'label' => '변경사항 저장',
                        ],

                    ],

                ],

            ],
            'reorder' => [
                'label' => '이동',
            ],

            'move_down' => [
                'label' => '아래로 이동',
            ],

            'move_up' => [
                'label' => '위로 이동',
            ],

            'collapse' => [
                'label' => '접기',
            ],

            'expand' => [
                'label' => '펼치기',
            ],

            'collapse_all' => [
                'label' => '모두 접기',
            ],

            'expand_all' => [
                'label' => '모두 펼치기',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => '모두 선택 해제',
            ],

            'select_all' => [
                'label' => '모두 선택',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => '취소',
                ],

                'drag_crop' => [
                    'label' => '자르기 모드',
                ],

                'drag_move' => [
                    'label' => '이동 모드',
                ],

                'flip_horizontal' => [
                    'label' => '가로로 뒤집기',
                ],

                'flip_vertical' => [
                    'label' => '세로로 뒤집기',
                ],

                'move_down' => [
                    'label' => '이미지를 아래로 이동',
                ],

                'move_left' => [
                    'label' => '이미지를 왼쪽으로 이동',
                ],

                'move_right' => [
                    'label' => '이미지를 오른쪽으로 이동',
                ],

                'move_up' => [
                    'label' => '이미지를 위로 이동',
                ],

                'reset' => [
                    'label' => '초기화',
                ],

                'rotate_left' => [
                    'label' => '왼쪽으로 회전',
                ],

                'rotate_right' => [
                    'label' => '오른쪽으로 회전',
                ],

                'set_aspect_ratio' => [
                    'label' => '화면비를 :ratio 로 설정',
                ],

                'save' => [
                    'label' => '저장',
                ],

                'zoom_100' => [
                    'label' => '이미지 100%로 확대',
                ],

                'zoom_in' => [
                    'label' => '확대',
                ],

                'zoom_out' => [
                    'label' => '축소',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => '세로',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => '회전',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => '가로',
                    'unit' => 'px',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'px',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'px',
                ],

            ],

            'aspect_ratios' => [

                'label' => '화면비',

                'no_fixed' => [
                    'label' => '자유',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG 파일을 편집하는 것은 크기 조정 시 품질 손실이 발생할 수 있으므로 권장되지 않습니다.\n 계속하시겠습니까?',
                    'disabled' => 'SVG 파일 편집은 크기 조정 시 품질 손실이 발생할 수 있으므로 비활성화되었습니다.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => '행 추가',
            ],

            'delete' => [
                'label' => '행 삭제',
            ],

            'reorder' => [
                'label' => '행 재정렬',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => '키',
            ],

            'value' => [
                'label' => '값',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => '파일 첨부',
            'blockquote' => '인용구',
            'bold' => '굵게',
            'bullet_list' => '순서가 없는 목록',
            'code_block' => '코드 블록',
            'heading' => '제목',
            'italic' => '기울임체',
            'link' => '링크',
            'ordered_list' => '번호 목록',
            'redo' => '다시 실행',
            'strike' => '취소선',
            'table' => '테이블',
            'undo' => '실행 취소',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => '선택',

                'actions' => [

                    'select' => [
                        'label' => '선택',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => '예',
            'false' => '아니오',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => ':label 추가',
            ],

            'add_between' => [
                'label' => '사이에 삽입',
            ],

            'delete' => [
                'label' => '삭제',
            ],

            'clone' => [
                'label' => '복제',
            ],

            'reorder' => [
                'label' => '이동',
            ],

            'move_down' => [
                'label' => '아래로 이동',
            ],

            'move_up' => [
                'label' => '위로 이동',
            ],

            'collapse' => [
                'label' => '접기',
            ],

            'expand' => [
                'label' => '펼치기',
            ],

            'collapse_all' => [
                'label' => '모두 접기',
            ],

            'expand_all' => [
                'label' => '모두 펼치기',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => '파일 업로드',

                'modal' => [

                    'heading' => '파일 업로드',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => '파일',
                                'existing' => '파일 교체',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => '대체 텍스트',
                                'existing' => '대체 텍스트 변경',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => '삽입',
                        ],

                        'save' => [
                            'label' => '저장',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => '수정',

                'modal' => [

                    'heading' => '링크',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => '새 탭에서 열기',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => '병합 태그 결과가 없습니다.',

        'tools' => [
            'align_center' => '가운데 정렬',
            'align_end' => '끝 정렬',
            'align_justify' => '양쪽 정렬',
            'align_start' => '시작 정렬',
            'attach_files' => '파일 첨부',
            'blockquote' => '인용구',
            'bold' => '굵게',
            'bullet_list' => '순서가 없는 목록',
            'clear_formatting' => '서식 지우기',
            'code' => '코드',
            'code_block' => '코드 블록',
            'custom_blocks' => '블록',
            'details' => '세부사항',
            'h1' => '큰 제목',
            'h2' => '중간 제목',
            'h3' => '작은 제목',
            'highlight' => '강조',
            'horizontal_rule' => '수평선',
            'italic' => '기울임체',
            'lead' => '강조 텍스트',
            'link' => '링크',
            'merge_tags' => '병합 태그',
            'ordered_list' => '번호 목록',
            'redo' => '다시 실행',
            'small' => '작은 텍스트',
            'strike' => '취소선',
            'subscript' => '아래 첨자',
            'superscript' => '위 첨자',
            'table' => '테이블',
            'table_delete' => '테이블 삭제',
            'table_add_column_before' => '이전에 열 추가',
            'table_add_column_after' => '이후에 열 추가',
            'table_delete_column' => '열 삭제',
            'table_add_row_before' => '위에 행 추가',
            'table_add_row_after' => '아래에 행 추가',
            'table_delete_row' => '행 삭제',
            'table_merge_cells' => '셀 병합',
            'table_split_cell' => '셀 분할',
            'table_toggle_header_row' => '헤더 행 토글',
            'underline' => '밑줄',
            'undo' => '실행 취소',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => '만들기',

                'modal' => [

                    'heading' => '새로 만들기',

                    'actions' => [

                        'create' => [
                            'label' => '만들기',
                        ],

                        'create_another' => [
                            'label' => '만들고 다른 것 만들기',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => '수정',

                'modal' => [

                    'heading' => '수정',

                    'actions' => [

                        'save' => [
                            'label' => '저장',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => '예',
            'false' => '아니오',
        ],

        'loading_message' => '로드 중...',

        'max_items_message' => ':count개까지만 선택할 수 있습니다.',

        'no_search_results_message' => '검색 결과가 없습니다.',

        'placeholder' => '옵션을 선택하세요',

        'searching_message' => '검색 중...',

        'search_prompt' => '검색어를 입력하세요...',
    ],

    'tags_input' => [
        'placeholder' => '새 태그',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => '복사',
                'message' => '복사됨',
            ],

            'hide_password' => [
                'label' => '비밀번호 숨기기',
            ],

            'show_password' => [
                'label' => '비밀번호 표시',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => '예',
            'false' => '아니오',
        ],

    ],

];

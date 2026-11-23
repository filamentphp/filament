<?php

return [

    'builder' => [

        'reordering' => [
            'item' => '블록',
            'position' => ':label, :count개 중 :position번째',
            'moved' => ':label 항목이 :count개 중 :position번째 위치로 이동했습니다',
        ],

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

        'block_picker' => [

            'no_search_results_message' => '검색과 일치하는 블록이 없습니다.',

            'search_prompt' => '블록 검색',

        ],

    ],

    'checkbox_list' => [

        'required_description' => '하나 이상의 옵션을 선택하세요.',

        'actions' => [

            'deselect_all' => [
                'label' => '모두 선택 해제',
            ],

            'select_all' => [
                'label' => '모두 선택',
            ],

        ],

    ],

    'color_picker' => [

        'panel_label' => '색상 선택기',

    ],

    'date_time_picker' => [

        'month_select' => [
            'label' => '월',
        ],

        'year_input' => [
            'label' => '연도',
        ],

        'hour_input' => [
            'label' => '시',
        ],

        'minute_input' => [
            'label' => '분',
        ],

        'second_input' => [
            'label' => '초',
        ],

    ],

    'file_upload' => [

        'placeholder' => '업로드된 파일 없음',

        'actions' => [

            'download' => [
                'label' => '다운로드',
            ],

            'open' => [
                'label' => '새 탭에서 열기',
            ],

        ],

        'editor' => [

            'label' => '이미지 편집기',

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

            'move_down' => [
                'label' => '아래로 이동',
            ],

            'move_up' => [
                'label' => '위로 이동',
            ],

            'reorder' => [
                'label' => '행 재정렬',
            ],

        ],

        'columns' => [

            'actions' => [
                'label' => '작업',
            ],

            'reorder' => [
                'label' => '순서 변경',
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

        'reordering' => [
            'row' => '행',
            'position' => ':label, :count개 중 :position번째',
            'moved' => ':label 항목이 :count개 중 :position번째 위치로 이동했습니다',
        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => '업로드할 수 있는 파일 형식: :values.',

        'file_attachments_max_size_message' => '업로드할 파일의 크기는 최대 :max킬로바이트입니다.',

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

        'reordering' => [
            'item' => '항목',
            'position' => ':label, :count개 중 :position번째',
            'moved' => ':label 항목이 :count개 중 :position번째 위치로 이동했습니다',
        ],

        'columns' => [

            'actions' => [
                'label' => '작업',
            ],

            'reorder' => [
                'label' => '순서 변경',
            ],

        ],

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

            'close_panel' => [
                'label' => '패널 닫기',
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

            'grid' => [

                'label' => '그리드',

                'modal' => [

                    'heading' => '그리드',

                    'form' => [

                        'preset' => [

                            'label' => '프리셋',

                            'placeholder' => '없음',

                            'options' => [
                                'two' => '2열',
                                'three' => '3열',
                                'four' => '4열',
                                'five' => '5열',
                                'two_start_third' => '2열 (시작 열 1/3)',
                                'two_end_third' => '2열 (끝 열 1/3)',
                                'two_start_fourth' => '2열 (시작 열 1/4)',
                                'two_end_fourth' => '2열 (끝 열 1/4)',
                            ],
                        ],

                        'columns' => [
                            'label' => '열',
                        ],

                        'from_breakpoint' => [

                            'label' => '적용 시작 브레이크포인트',

                            'options' => [
                                'default' => '전체',
                                'sm' => '소형',
                                'md' => '중형',
                                'lg' => '대형',
                                'xl' => '특대형',
                                '2xl' => '초특대형',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => '비대칭 2열',
                        ],

                        'start_span' => [
                            'label' => '시작 열 너비',
                        ],

                        'end_span' => [
                            'label' => '끝 열 너비',
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

            'text_color' => [

                'label' => '텍스트 색상',

                'modal' => [

                    'heading' => '텍스트 색상',

                    'form' => [

                        'color' => [
                            'label' => '색상',

                            'options' => [
                                'slate' => '슬레이트',
                                'gray' => '회색',
                                'zinc' => '징크',
                                'neutral' => '뉴트럴',
                                'stone' => '스톤',
                                'mauve' => '모브',
                                'olive' => '올리브',
                                'mist' => '미스트',
                                'taupe' => '토프',
                                'red' => '빨간색',
                                'orange' => '주황색',
                                'amber' => '앰버',
                                'yellow' => '노란색',
                                'lime' => '라임',
                                'green' => '초록색',
                                'emerald' => '에메랄드',
                                'teal' => '틸',
                                'cyan' => '시안',
                                'sky' => '하늘색',
                                'blue' => '파란색',
                                'indigo' => '인디고',
                                'violet' => '바이올렛',
                                'purple' => '보라색',
                                'fuchsia' => '푸시아',
                                'pink' => '분홍색',
                                'rose' => '로즈',
                            ],
                        ],

                        'custom_color' => [
                            'label' => '사용자 지정 색상',
                        ],

                    ],

                ],

            ],

        ],

        'custom_blocks' => [

            'actions' => [

                'delete' => [
                    'label' => '블록 삭제',
                ],

                'edit' => [
                    'label' => '블록 수정',
                ],

            ],

            'no_search_results_message' => '검색과 일치하는 블록이 없습니다.',

            'search_label' => '블록 검색',

            'search_prompt' => '블록 검색',

        ],

        'file_attachments_accepted_file_types_message' => '업로드할 수 있는 파일 형식: :values.',

        'file_attachments_max_size_message' => '업로드할 파일의 크기는 최대 :max킬로바이트입니다.',

        'no_merge_tag_search_results_message' => '병합 태그 결과가 없습니다.',

        'mentions' => [
            'no_options_message' => '사용 가능한 옵션이 없습니다.',
            'no_search_results_message' => '검색 결과가 없습니다.',
            'search_prompt' => '검색어를 입력하세요',
            'searching_message' => '검색 중...',
        ],

        'toolbar' => [
            'label' => '편집기 도구 모음',
        ],

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
            'h4' => '제목 4',
            'h5' => '제목 5',
            'h6' => '제목 6',
            'grid' => '그리드',
            'grid_delete' => '그리드 삭제',
            'highlight' => '강조',
            'horizontal_rule' => '수평선',
            'italic' => '기울임체',
            'lead' => '강조 텍스트',
            'link' => '링크',
            'merge_tags' => '병합 태그',
            'ordered_list' => '번호 목록',
            'paragraph' => '단락',
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
            'table_toggle_header_cell' => '헤더 셀 토글',
            'text_color' => '텍스트 색상',
            'underline' => '밑줄',
            'undo' => '실행 취소',
        ],

        'uploading_file_message' => '파일 업로드 중...',

    ],

    'select' => [

        'actions' => [

            'clear' => [
                'label' => '선택 해제',
            ],

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

            'remove_option' => [
                'label' => ':label 제거',
            ],

        ],

        'boolean' => [
            'true' => '예',
            'false' => '아니오',
        ],

        'error_message' => '옵션을 불러오지 못했습니다. 다시 시도해 주세요.',

        'loading_message' => '로드 중...',

        'max_items_message' => ':count개까지만 선택할 수 있습니다.',

        'no_options_message' => '사용 가능한 옵션이 없습니다.',

        'no_search_results_message' => '검색 결과가 없습니다.',

        'placeholder' => '옵션을 선택하세요',

        'required_description' => '옵션을 선택하세요.',

        'searching_message' => '검색 중...',

        'search_label' => '검색',

        'search_prompt' => '검색어를 입력하세요...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => '삭제',
            ],

        ],

        'placeholder' => '새 태그',

        'tag_added' => '추가됨: :tag',

        'tag_removed' => '제거됨: :tag',

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

        'required_description' => '하나 이상의 옵션을 선택하세요.',

        'boolean' => [
            'true' => '예',
            'false' => '아니오',
        ],

    ],

];

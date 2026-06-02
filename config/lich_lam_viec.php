<?php

return [
    /*
    | Tên đệm (từ cuối của họ tên) nằm trong danh sách này thì hiển thị rút gọn
    | gồm tên đệm + tên (vd. Nguyễn Văn Anh → Văn Anh thay vì Anh).
    */
    'ten_rut_gon_dac_biet' => [
        'Anh',
    ],

    /*
    | Màu thẻ hợp đồng trên lịch làm việc (theo tiến độ cao nhất đạt được).
    */
    'tien_do' => [
        'phan_chup' => [
            'label' => 'Đã phân chụp',
            'bg' => '#ffe4e8',
            'border' => '#e53e3e',
        ],
        'phan_make' => [
            'label' => 'Đã phân make',
            'bg' => '#ede9fe',
            'border' => '#7c3aed',
        ],
        'phan_edit' => [
            'label' => 'Đã phân edit',
            'bg' => '#dbeafe',
            'border' => '#2563eb',
        ],
        'up_link_demo' => [
            'label' => 'Đã up link demo',
            'bg' => '#d1fae5',
            'border' => '#059669',
        ],
        'up_link_in' => [
            'label' => 'Đã up link in',
            'bg' => '#fef3c7',
            'border' => '#d97706',
        ],
    ],

    /*
    | Bộ lọc lịch làm việc (checkbox). Không chọn = tất cả HĐ (trừ nháp).
    | Chọn nhiều = OR — HĐ thỏa ít nhất một điều kiện.
    */
    'loc_tien_do' => [
        'phan_chup' => ['label' => 'Đã phân chụp'],
        'phan_make' => ['label' => 'Đã phân make'],
        'phan_edit' => ['label' => 'Đã phân edit'],
        'up_link_demo' => ['label' => 'Đã up link demo'],
        'up_link_in' => ['label' => 'Đã up link in'],
        'da_nhan_coc' => ['label' => 'Đã nhận cọc'],
        'da_tat_toan' => ['label' => 'Đã tất toán'],
    ],
];

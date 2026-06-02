<?php

/**
 * Cấu trúc menu sidebar admin.
 * Mỗi tài khoản chỉ thấy các mục có route nằm trong vai_tro.ds_menu (theo user.role → ma_vai_tro).
 * Admin thấy toàn bộ.
 */
return [
    [
        'stt' => 1,
        'type' => 'single',
        'route' => 'admin.index',
        'label' => 'Tổng quan',
        'icon' => 'ti tabler-smart-home',
    ],
    [
        'stt' => 2,
        'type' => 'single',
        'route' => 'admin.khach-hang.tao-hop-dong',
        'label' => 'Tạo hợp đồng cưới',
        'icon' => 'ti tabler-file-plus',
    ],
    [
        'stt' => 3,
        'type' => 'single',
        'route' => 'admin.khach-hang.danh-sach-hop-dong-cuoi',
        'label' => 'DS hợp đồng cưới',
        'icon' => 'ti tabler-list-details',
    ],
    [
        'stt' => 4,
        'type' => 'single',
        'route' => 'admin.trang-phuc.hop-dong',
        'label' => 'DS hợp đồng thuê',
        'icon' => 'ti tabler-hanger',
    ],
    [
        'stt' => 5,
        'type' => 'single',
        'route' => 'admin.lich-lam-viec',
        'label' => 'Lịch làm việc',
        'icon' => 'ti tabler-calendar',
    ],
    [
        'stt' => 6,
        'type' => 'single',
        'route' => 'admin.diem-danh.diem-danh',
        'label' => 'Điểm danh',
        'icon' => 'ti tabler-clipboard-check',
    ],
    [
        'stt' => 7,
        'type' => 'single',
        'route' => 'admin.diem-danh.cham-cong',
        'label' => 'Chấm công',
        'icon' => 'ti tabler-clock',
    ],
    [
        'stt' => 8,
        'type' => 'group',
        'routes' => [
            'admin.nhan-su.danh-sach',
            'admin.nhan-su.cong-viec-cua-toi',
        ],
        'route_prefix' => 'admin.nhan-su.',
        'label' => 'Nhân sự',
        'icon' => 'ti tabler-users',
        'children' => [
            ['stt' => 1, 'route' => 'admin.nhan-su.danh-sach', 'label' => 'Danh sách nhân sự'],
            ['stt' => 2, 'route' => 'admin.nhan-su.cong-viec-cua-toi', 'label' => 'Công việc của tôi'],
        ],
    ],
    [
        'stt' => 9,
        'type' => 'group',
        'routes' => [
            'admin.trang-phuc.san-pham',
            'admin.concept.concept',
            'admin.dich-vu.dich-vu-le',
            'admin.dich-vu.nhom-dich-vu',
        ],
        'route_prefixes' => ['admin.trang-phuc.', 'admin.concept.', 'admin.dich-vu.'],
        'label' => 'Sản phẩm',
        'icon' => 'ti tabler-package',
        'children' => [
            ['stt' => 1, 'route' => 'admin.trang-phuc.san-pham', 'label' => 'Trang phục'],
            ['stt' => 2, 'route' => 'admin.concept.concept', 'label' => 'Concept'],
            ['stt' => 3, 'route' => 'admin.dich-vu.dich-vu-le', 'label' => 'Dịch vụ lẻ'],
            ['stt' => 4, 'route' => 'admin.dich-vu.nhom-dich-vu', 'label' => 'Nhóm dịch vụ'],
        ],
    ],
    [
        'stt' => 10,
        'type' => 'group',
        'routes' => ['admin.tai-chinh.cong-no', 'admin.tai-chinh.phieu-thu-chi', 'admin.tai-chinh.tinh-luong'],
        'route_prefix' => 'admin.tai-chinh.',
        'label' => 'Tài chính kế toán',
        'icon' => 'ti tabler-cash',
        'children' => [
            ['stt' => 1, 'route' => 'admin.tai-chinh.cong-no', 'label' => 'Công nợ'],
            ['stt' => 2, 'route' => 'admin.tai-chinh.phieu-thu-chi', 'label' => 'Phiếu thu chi'],
            ['stt' => 3, 'route' => 'admin.tai-chinh.tinh-luong', 'label' => 'Tính lương'],
        ],
    ],
    [
        'stt' => 11,
        'type' => 'single',
        'route' => 'admin.note-khach-moi',
        'label' => 'Note khách mới',
        'icon' => 'ti tabler-notes',
    ],
    [
        'stt' => 12,
        'type' => 'group',
        'routes' => ['admin.bao-cao.ads'],
        'route_prefix' => 'admin.bao-cao.',
        'label' => 'Báo cáo',
        'icon' => 'ti tabler-report-analytics',
        'children' => [
            ['stt' => 1, 'route' => 'admin.bao-cao.ads', 'label' => 'Báo cáo Ads'],
        ],
    ],
    [
        'stt' => 13,
        'type' => 'single',
        'route' => 'admin.tu-van.danh-sach',
        'label' => 'Tư vấn',
        'icon' => 'ti tabler-message-circle',
    ],
    [
        'stt' => 14,
        'type' => 'single',
        'route' => 'admin.thong-tin-ca-nhan',
        'label' => 'Thông tin cá nhân',
        'icon' => 'ti tabler-id',
    ],
    [
        'stt' => 15,
        'type' => 'group',
        'routes' => ['admin.he-thong.vai-tro', 'admin.he-thong.phong-ban', 'admin.he-thong.tai-lieu', 'admin.he-thong.logs'],
        'route_prefix' => 'admin.he-thong.',
        'label' => 'Hệ thống',
        'icon' => 'ti tabler-building',
        'children' => [
            ['stt' => 1, 'route' => 'admin.he-thong.vai-tro', 'label' => 'Vai trò'],
            ['stt' => 2, 'route' => 'admin.he-thong.phong-ban', 'label' => 'Phòng ban'],
            ['stt' => 3, 'route' => 'admin.he-thong.tai-lieu', 'label' => 'Tài liệu'],
            ['stt' => 4, 'route' => 'admin.he-thong.logs', 'label' => 'Logs'],
        ],
    ],
];

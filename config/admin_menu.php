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
        'type' => 'group',
        'routes' => [
            'admin.khach-hang.tao-hop-dong',
            'admin.khach-hang.danh-sach-hop-dong-cuoi',
            'admin.trang-phuc.hop-dong',
        ],
        'route_prefixes' => ['admin.khach-hang.'],
        'label' => 'Hợp đồng',
        'icon' => 'ti tabler-file-text',
        'children' => [
            ['stt' => 1, 'route' => 'admin.khach-hang.tao-hop-dong', 'label' => 'Tạo hợp đồng cưới', 'icon' => 'ti tabler-file-plus'],
            ['stt' => 2, 'route' => 'admin.khach-hang.danh-sach-hop-dong-cuoi', 'label' => 'DS hợp đồng cưới', 'icon' => 'ti tabler-list-details'],
            ['stt' => 3, 'route' => 'admin.trang-phuc.hop-dong', 'label' => 'DS hợp đồng thuê', 'icon' => 'ti tabler-hanger'],
        ],
    ],
    [
        'stt' => 3,
        'type' => 'group',
        'routes' => [
            'admin.lich-chup',
            'admin.lich-shop',
        ],
        'label' => 'Lịch làm việc',
        'icon' => 'ti tabler-calendar',
        'children' => [
            ['stt' => 1, 'route' => 'admin.lich-chup', 'label' => 'Lịch chụp', 'icon' => 'ti tabler-calendar'],
            ['stt' => 2, 'route' => 'admin.lich-shop', 'label' => 'Lịch shop', 'icon' => 'ti tabler-calendar-event'],
        ],
    ],
    [
        'stt' => 4,
        'type' => 'single',
        'route' => 'admin.diem-danh.diem-danh',
        'label' => 'Điểm danh',
        'icon' => 'ti tabler-clipboard-check',
    ],
    [
        'stt' => 5,
        'type' => 'single',
        'route' => 'admin.diem-danh.cham-cong',
        'label' => 'Chấm công',
        'icon' => 'ti tabler-clock',
    ],
    [
        'stt' => 6,
        'type' => 'single',
        'route' => 'admin.diem-danh.nghi-phep',
        'label' => 'Nghỉ phép',
        'icon' => 'ti tabler-beach',
    ],
    [
        'stt' => 7,
        'type' => 'group',
        'routes' => [
            'admin.nhan-su.danh-sach',
            'admin.nhan-su.cong-viec-cua-toi',
        ],
        'route_prefix' => 'admin.nhan-su.',
        'label' => 'Nhân sự',
        'icon' => 'ti tabler-users',
        'children' => [
            ['stt' => 1, 'route' => 'admin.nhan-su.danh-sach', 'label' => 'Danh sách nhân sự', 'icon' => 'ti tabler-users'],
            ['stt' => 2, 'route' => 'admin.nhan-su.cong-viec-cua-toi', 'label' => 'Công việc của tôi', 'icon' => 'ti tabler-briefcase'],
        ],
    ],
    [
        'stt' => 8,
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
            ['stt' => 1, 'route' => 'admin.trang-phuc.san-pham', 'label' => 'Trang phục', 'icon' => 'ti tabler-hanger'],
            ['stt' => 2, 'route' => 'admin.concept.concept', 'label' => 'Concept', 'icon' => 'ti tabler-photo'],
            ['stt' => 3, 'route' => 'admin.dich-vu.dich-vu-le', 'label' => 'Dịch vụ lẻ', 'icon' => 'ti tabler-puzzle'],
            ['stt' => 4, 'route' => 'admin.dich-vu.nhom-dich-vu', 'label' => 'Nhóm dịch vụ', 'icon' => 'ti tabler-stack-2'],
        ],
    ],
    [
        'stt' => 9,
        'type' => 'group',
        'routes' => ['admin.tai-chinh.cong-no', 'admin.tai-chinh.phieu-thu-chi', 'admin.tai-chinh.tinh-luong'],
        'route_prefix' => 'admin.tai-chinh.',
        'label' => 'Tài chính kế toán',
        'icon' => 'ti tabler-cash',
        'children' => [
            ['stt' => 1, 'route' => 'admin.tai-chinh.cong-no', 'label' => 'Công nợ', 'icon' => 'ti tabler-receipt'],
            ['stt' => 2, 'route' => 'admin.tai-chinh.phieu-thu-chi', 'label' => 'Phiếu thu chi', 'icon' => 'ti tabler-file-invoice'],
            ['stt' => 3, 'route' => 'admin.tai-chinh.tinh-luong', 'label' => 'Tính lương', 'icon' => 'ti tabler-calculator'],
        ],
    ],
    [
        'stt' => 10,
        'type' => 'single',
        'route' => 'admin.note-khach-moi',
        'label' => 'Note khách mới',
        'icon' => 'ti tabler-notes',
    ],
    [
        'stt' => 11,
        'type' => 'group',
        'routes' => ['admin.bao-cao.ads'],
        'route_prefix' => 'admin.bao-cao.',
        'label' => 'Báo cáo',
        'icon' => 'ti tabler-report-analytics',
        'children' => [
            ['stt' => 1, 'route' => 'admin.bao-cao.ads', 'label' => 'Báo cáo Ads', 'icon' => 'ti tabler-chart-line'],
        ],
    ],
    [
        'stt' => 12,
        'type' => 'single',
        'route' => 'admin.tu-van.danh-sach',
        'label' => 'Tư vấn',
        'icon' => 'ti tabler-message-circle',
    ],
    [
        'stt' => 13,
        'type' => 'single',
        'route' => 'admin.thong-tin-ca-nhan',
        'label' => 'Thông tin cá nhân',
        'icon' => 'ti tabler-id',
    ],
    [
        'stt' => 14,
        'type' => 'group',
        'routes' => ['admin.he-thong.vai-tro', 'admin.he-thong.phong-ban', 'admin.he-thong.ca-lam-viec', 'admin.he-thong.ip-diem-danh', 'admin.he-thong.tai-lieu', 'admin.he-thong.logs'],
        'route_prefix' => 'admin.he-thong.',
        'label' => 'Hệ thống',
        'icon' => 'ti tabler-building',
        'children' => [
            ['stt' => 1, 'route' => 'admin.he-thong.vai-tro', 'label' => 'Vai trò', 'icon' => 'ti tabler-shield'],
            ['stt' => 2, 'route' => 'admin.he-thong.phong-ban', 'label' => 'Phòng ban', 'icon' => 'ti tabler-building'],
            ['stt' => 3, 'route' => 'admin.he-thong.ca-lam-viec', 'label' => 'Ca làm việc', 'icon' => 'ti tabler-clock-hour-4'],
            ['stt' => 4, 'route' => 'admin.he-thong.ip-diem-danh', 'label' => 'Ip điểm danh', 'icon' => 'ti tabler-network'],
            ['stt' => 5, 'route' => 'admin.he-thong.tai-lieu', 'label' => 'Tài liệu', 'icon' => 'ti tabler-file-text'],
            ['stt' => 6, 'route' => 'admin.he-thong.logs', 'label' => 'Logs', 'icon' => 'ti tabler-list'],
        ],
    ],
];

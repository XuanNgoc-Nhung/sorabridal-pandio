<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Khung ngày chốt lương tháng trước
    |--------------------------------------------------------------------------
    |
    | Trong khoảng ngày này (tính theo ngày trong tháng hiện tại), hệ thống
    | cho phép chốt lương của tháng trước. Mặc định: từ ngày 1 đến ngày 10.
    |
    | Ví dụ: ngày 5/6 có thể chốt lương tháng 5; ngày 15/6 thì không chốt được.
    |
    */
    'chot_luong' => [
        'ngay_bat_dau' => (int) env('TINH_LUONG_CHOT_NGAY_BAT_DAU', 1),
        'ngay_ket_thuc' => (int) env('TINH_LUONG_CHOT_NGAY_KET_THUC', 10),
    ],

];

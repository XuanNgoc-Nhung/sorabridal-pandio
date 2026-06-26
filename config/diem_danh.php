<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Số phút tăng ca tối thiểu (N)
    |--------------------------------------------------------------------------
    |
    | Chỉ tính lương tăng ca khi số phút làm tăng ca >= N (áp dụng full-time & part-time).
    |
    */
    'phut_tang_ca_toi_thieu' => (int) env('DIEM_DANH_PHUT_TANG_CA_TOI_THIEU', 30),

    /*
    |--------------------------------------------------------------------------
    | Giờ chuyển sang tăng ca (part-time)
    |--------------------------------------------------------------------------
    |
    | Thời gian trước mốc này tính lương cơ bản; từ mốc này trở đi tính tăng ca.
    |
    */
    'gio_chuyen_tang_ca' => env('DIEM_DANH_GIO_CHUYEN_TANG_CA', '21:00'),

];

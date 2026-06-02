<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NganHangThanhToan extends Model
{
    use HasFactory;

    protected $table = 'ngan_hang_thanh_toan';

    public const TRANG_THAI_NGUNG_HOAT_DONG = 0;
    public const TRANG_THAI_DANG_HOAT_DONG = 1;

    protected $fillable = [
        'hinh_anh_logo',
        'ten_ngan_hang',
        'ten_chi_tiet',
        'so_tai_khoan',
        'chu_tai_khoan',
        'chi_nhanh',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'trang_thai' => 'integer',
        ];
    }
}

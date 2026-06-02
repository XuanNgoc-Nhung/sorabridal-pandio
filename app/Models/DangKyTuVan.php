<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DangKyTuVan extends Model
{
    use HasFactory;

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_NGAY_CUOI = 'ngay_cuoi_du_kien';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_CREATED_AT;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_NGAY_CUOI => 'Ngày cưới',
    ];

    protected $table = 'dang_ky_tu_van';

    public const PHIM_TRUONG_OPTIONS = [
        'Sora Rooftop (Hồ Tây)',
        'Biệt Thự Sora (Colonial Villa)',
        'Cả hai phim trường',
        'Chưa quyết định',
    ];

    public const GOI_DICH_VU_OPTIONS = [
        'Gói Dấu yêu (6.3 triệu)',
        'Gói Cảm xúc (11.9 triệu)',
        'Gói Trọn vẹn (16.8 triệu)',
        'Tư vấn thêm',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ten_co_dau',
        'ten_chu_re',
        'so_dien_thoai',
        'ngay_cuoi_du_kien',
        'phim_truong_quan_tam',
        'goi_dich_vu_quan_tam',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'ngay_cuoi_du_kien' => 'date',
        ];
    }
}

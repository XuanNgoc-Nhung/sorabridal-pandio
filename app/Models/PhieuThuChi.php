<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhieuThuChi extends Model
{
    use HasFactory;

    protected $table = 'phieu_thu_chi';

    /** Loại phiếu: thu */
    public const LOAI_THU = 1;

    /** Loại phiếu: chi */
    public const LOAI_CHI = 2;

    /** Trạng thái: chờ xử lý */
    public const TRANG_THAI_CHO_XU_LY = 0;

    /** Trạng thái: đồng ý */
    public const TRANG_THAI_DONG_Y = 1;

    /** Trạng thái: từ chối */
    public const TRANG_THAI_TU_CHOI = -1;

    /** Trạng thái: hoàn thành */
    public const TRANG_THAI_HOAN_THANH = 2;

    public const SAP_XEP_SO_TIEN = 'so_tien';

    public const SAP_XEP_NGAY_DUYET = 'ngay_duyet';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_NGAY_DUYET;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_SO_TIEN => 'Số tiền',
        self::SAP_XEP_NGAY_DUYET => 'Ngày duyệt',
    ];

    protected $fillable = [
        'nguoi_tao_id',
        'nguoi_duyet_id',
        'loai_phieu',
        'so_tien',
        'ly_do',
        'trang_thai',
        'ghi_chu',
        'ngay_duyet',
    ];

    protected function casts(): array
    {
        return [
            'so_tien' => 'decimal:2',
            'loai_phieu' => 'integer',
            'trang_thai' => 'integer',
            'ngay_duyet' => 'datetime',
        ];
    }

    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id', 'id');
    }

    public function nguoiDuyet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_duyet_id', 'id');
    }
}

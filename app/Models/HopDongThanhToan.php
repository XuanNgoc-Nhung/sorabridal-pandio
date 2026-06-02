<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HopDongThanhToan extends Model
{
    protected $table = 'hop_dong_thanh_toan';

    public const HINH_THUC_CHUYEN_KHOAN = 'chuyen_khoan';

    public const HINH_THUC_TIEN_MAT = 'tien_mat';

    /**
     * proof_urls: mảng đường dẫn tương đối trong storage public (vd. hop-dong-thanh-toan/1/xxx.jpg).
     */
    protected $fillable = [
        'hop_dong_id',
        'lan_thanh_toan',
        'so_tien',
        'ngay_thanh_toan',
        'hinh_thuc_thanh_toan',
        'proof_urls',
        'ghi_chu',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'lan_thanh_toan' => 'integer',
            'so_tien' => 'decimal:2',
            'ngay_thanh_toan' => 'date',
            'proof_urls' => 'array',
            'created_at' => 'datetime',
            'created_by' => 'integer',
        ];
    }

    const UPDATED_AT = null;

    public function hopDongCuoi(): BelongsTo
    {
        return $this->belongsTo(HopDongCuoi::class, 'hop_dong_id');
    }

    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'created_by');
    }
}

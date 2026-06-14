<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanhVienHopDongCuoi extends Model
{
    protected $table = 'hop_dong_cuoi_thanh_vien_sale';

    public const VAI_TRO_NGUOI_TAO = 'nguoi_tao';

    public const VAI_TRO_THANH_VIEN = 'thanh_vien';

    /** @var array<string, string> */
    public const VAI_TRO_LABELS = [
        self::VAI_TRO_NGUOI_TAO => 'Người tạo',
        self::VAI_TRO_THANH_VIEN => 'Thành viên',
    ];

    protected $fillable = [
        'hop_dong_id',
        'nhan_vien_id',
        'vai_tro',
    ];

    public function hopDongCuoi(): BelongsTo
    {
        return $this->belongsTo(HopDongCuoi::class, 'hop_dong_id');
    }

    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    public static function vaiTroLabel(?string $vaiTro): string
    {
        if ($vaiTro === null || $vaiTro === '') {
            return '—';
        }

        return self::VAI_TRO_LABELS[$vaiTro] ?? $vaiTro;
    }
}

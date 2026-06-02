<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanhVienHopDongCuoi extends Model
{
    protected $table = 'hop_dong_cuoi_thanh_vien_sale';

    public const VAI_TRO_NGUOI_TAO = 'nguoi_tao';

    public const VAI_TRO_THANH_VIEN = 'thanh_vien';

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
}

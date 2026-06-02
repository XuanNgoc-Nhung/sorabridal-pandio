<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HopDongCuoiTrangPhuc extends Model
{
    protected $table = 'hop_dong_cuoi_trang_phuc';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hop_dong_cuoi_id',
        'trang_phuc_id',
    ];

    public function hopDongCuoi(): BelongsTo
    {
        return $this->belongsTo(HopDongCuoi::class, 'hop_dong_cuoi_id');
    }

    public function trangPhuc(): BelongsTo
    {
        return $this->belongsTo(TrangPhuc::class, 'trang_phuc_id');
    }
}

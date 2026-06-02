<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HopDongCuoiNhomDichVu extends Model
{
    use HasFactory;

    protected $table = 'hop_dong_cuoi_nhom_dich_vu';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'dich_vu_le_id',
        'nhom_dich_vu_id',
        'hop_dong_cuoi_id',
        'trang_thai_su_dung',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trang_thai_su_dung' => 'integer',
        ];
    }

    public function hopDongCuoi(): BelongsTo
    {
        return $this->belongsTo(HopDongCuoi::class, 'hop_dong_cuoi_id', 'id');
    }

    public function dichVuLe(): BelongsTo
    {
        return $this->belongsTo(DichVuLe::class, 'dich_vu_le_id', 'id');
    }

    public function nhomDichVu(): BelongsTo
    {
        return $this->belongsTo(NhomDichVu::class, 'nhom_dich_vu_id', 'id');
    }
}

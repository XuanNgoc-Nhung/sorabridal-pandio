<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HopDongCuoiDichVuLe extends Model
{
    use HasFactory;

    protected $table = 'hop_dong_cuoi_dich_vu_le';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'dich_vu_le_id',
        'hop_dong_cuoi_id',
        'so_luong',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'so_luong' => 'integer',
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
}

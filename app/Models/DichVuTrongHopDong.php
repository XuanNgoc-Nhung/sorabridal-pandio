<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DichVuTrongHopDong extends Model
{
    use HasFactory;

    protected $table = 'dich_vu_trong_hop_dong';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_hop_dong',
        'id_dich_vu',
        'gia_goc',
        'gia_thuc',
        'so_luong',
        'thanh_tien',
        'ghi_chu',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gia_goc' => 'decimal:2',
            'gia_thuc' => 'decimal:2',
            'so_luong' => 'integer',
            'thanh_tien' => 'decimal:2',
        ];
    }

    /** Hợp đồng. */
    public function hopDong(): BelongsTo
    {
        return $this->belongsTo(HopDong::class, 'id_hop_dong', 'id');
    }

    /** Dịch vụ lẻ. */
    public function dichVu(): BelongsTo
    {
        return $this->belongsTo(DichVuLe::class, 'id_dich_vu', 'id');
    }
}

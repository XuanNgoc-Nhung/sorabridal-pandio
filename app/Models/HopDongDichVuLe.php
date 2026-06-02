<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class HopDongDichVuLe extends Pivot
{
    protected $table = 'hop_dong_dich_vu_le';

    protected $foreignKey = 'hop_dong_id';

    protected $relatedKey = 'dich_vu_le_id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hop_dong_id',
        'dich_vu_le_id',
        'gia_goc',
        'gia_thuc',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gia_goc' => 'decimal:2',
            'gia_thuc' => 'decimal:2',
        ];
    }
}

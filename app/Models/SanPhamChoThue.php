<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SanPhamChoThue extends Model
{
    use HasFactory;

    protected $table = 'san_pham_cho_thue';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hop_dong_id',
        'san_pham_id',
        'ghi_chu',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hop_dong_id' => 'integer',
            'san_pham_id' => 'integer',
        ];
    }

    public function hopDong(): BelongsTo
    {
        return $this->belongsTo(HopDongChoThueTrangPhuc::class, 'hop_dong_id', 'id');
    }

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(TrangPhuc::class, 'san_pham_id', 'id');
    }
}

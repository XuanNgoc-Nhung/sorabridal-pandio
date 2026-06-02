<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiemDanh extends Model
{
    use HasFactory;

    protected $table = 'diem_danh';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'gio_vao',
        'gio_ra',
        'di_muon',
        'hop_le',
        'ly_do',
        'nghi_phep',
        'loai_phep',
        'ghi_chu',
        'gio_lam_co_ban',
        'gio_lam_tang_ca',
        'luong_co_ban',
        'luong_tang_ca',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gio_vao' => 'datetime',
            'gio_ra' => 'datetime',
            'di_muon' => 'boolean',
            'hop_le' => 'boolean',
            'nghi_phep' => 'boolean',
            'gio_lam_co_ban' => 'decimal:2',
            'gio_lam_tang_ca' => 'decimal:2',
            'luong_co_ban' => 'decimal:2',
            'luong_tang_ca' => 'decimal:2',
        ];
    }

    /**
     * Liên kết với bảng users (người điểm danh).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chamCong(): HasMany
    {
        return $this->hasMany(ChamCong::class, 'diem_danh_id', 'id');
    }
}

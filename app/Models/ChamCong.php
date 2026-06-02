<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChamCong extends Model
{
    use HasFactory;

    protected $table = 'cham_cong';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'diem_danh_id',
        'ngay_diem_danh',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ngay_diem_danh' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diemDanh(): BelongsTo
    {
        return $this->belongsTo(DiemDanh::class, 'diem_danh_id', 'id');
    }
}


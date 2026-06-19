<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DangKyCaLamViec extends Model
{
    use HasFactory;

    protected $table = 'dang_ky_ca_lam_viec';

    /** @var list<string> */
    protected $fillable = [
        'ca_lam_id',
        'nguoi_dung_id',
        'ngay_lam',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ngay_lam' => 'date',
        ];
    }

    public function caLamViec(): BelongsTo
    {
        return $this->belongsTo(CaLamViec::class, 'ca_lam_id', 'id');
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NhanVien extends Model
{
    use HasFactory;

    protected $table = 'nhan_vien';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hinh_anh',
        'user_id',
        'phong_ban',
        'ngan_hang',
        'chi_nhanh',
        'so_tai_khoan',
        'gioi_tinh',
        'ngay_sinh',
        'cccd',
        'vi_tri_lam_viec',
        'ngay_vao_cong_ty',
        'ngay_ky_hop_dong',
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
            'ngay_sinh' => 'date',
            'ngay_vao_cong_ty' => 'date',
            'ngay_ky_hop_dong' => 'date',
            'luong_co_ban' => 'integer',
            'luong_tang_ca' => 'integer',
        ];
    }

    /**
     * Liên kết với bảng users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Liên kết nhiều-nhiều với phòng ban (một nhân viên có thể trực thuộc nhiều phòng ban).
     */
    public function phongBans(): BelongsToMany
    {
        return $this->belongsToMany(PhongBan::class, 'nhan_vien_phong_ban')->withTimestamps();
    }
}

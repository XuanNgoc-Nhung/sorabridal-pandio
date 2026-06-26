<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NhanVien extends Model
{
    use HasFactory;

    public const LOAI_NHAN_VIEN_FULL_TIME = 'full_time';

    public const LOAI_NHAN_VIEN_PART_TIME = 'part_time';

    /** @var array<string, string> */
    public const LOAI_NHAN_VIEN_OPTIONS = [
        self::LOAI_NHAN_VIEN_FULL_TIME => 'Full-time',
        self::LOAI_NHAN_VIEN_PART_TIME => 'Part-time',
    ];

    public const LOAI_HOP_DONG_CHINH_THUC = 'chinh_thuc';

    public const LOAI_HOP_DONG_THU_VIEC = 'thu_viec';

    public const LOAI_HOP_DONG_HOC_VIEC = 'hoc_viec';

    public const LOAI_HOP_DONG_THUC_TAP = 'thuc_tap';

    /** @var array<string, string> */
    public const LOAI_HOP_DONG_OPTIONS = [
        self::LOAI_HOP_DONG_CHINH_THUC => 'Chính thức',
        self::LOAI_HOP_DONG_THU_VIEC => 'Thử việc',
        self::LOAI_HOP_DONG_HOC_VIEC => 'Học việc',
        self::LOAI_HOP_DONG_THUC_TAP => 'Thực tập',
    ];

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
        'chu_tai_khoan',
        'gioi_tinh',
        'ngay_sinh',
        'cccd',
        'vi_tri_lam_viec',
        'ngay_vao_cong_ty',
        'ngay_ky_hop_dong',
        'loai_nhan_vien',
        'loai_hop_dong',
        'luong_cung',
        'luong_mem',
        'phu_cap',
        'luong_co_ban',
        'luong_tang_ca',
        'hoa_hong_hop_dong_cuoi',
        'hoa_hong_hop_dong_trang_phuc',
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
            'luong_cung' => 'integer',
            'luong_mem' => 'integer',
            'phu_cap' => 'integer',
            'luong_co_ban' => 'integer',
            'luong_tang_ca' => 'integer',
            'hoa_hong_hop_dong_cuoi' => 'decimal:2',
            'hoa_hong_hop_dong_trang_phuc' => 'decimal:2',
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
     * Liên kết với phòng ban qua mã phòng ban.
     */
    public function phongBan(): BelongsTo
    {
        return $this->belongsTo(PhongBan::class, 'phong_ban', 'ma_phong_ban');
    }

    /**
     * Lọc nhân viên thuộc phòng ban theo mã (ví dụ CS01, MS01, PS01).
     */
    public function scopeThuocMaPhongBan($query, string $maPhongBan)
    {
        return $query->where('phong_ban', $maPhongBan);
    }
}

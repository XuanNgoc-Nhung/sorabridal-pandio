<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HopDong extends Model
{
    use HasFactory;

    protected $table = 'hop_dong';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ma_hop_dong',
        'nguoi_tao_id',
        'tho_chup_id',
        'tho_make_id',
        'tho_edit_id',
        'dia_diem',
        'ngay_chup',
        'trang_phuc',
        'concept',
        'ghi_chu_chup',
        'trang_thai_chup',
        'tong_tien',
        'nguoi_gioi_thieu',
        'so_tien_giam_gia',
        'thanh_toan_lan_1',
        'thanh_toan_lan_2',
        'thanh_toan_lan_3',
        'anh_thanh_toan_1',
        'anh_thanh_toan_2',
        'anh_thanh_toan_3',
        'trang_thai_hop_dong',
        'trang_thai_edit',
        'link_file_demo',
        'link_file_in',
        'ngay_tra_link_in',
        'ngay_hen_tra_hang',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ngay_chup' => 'datetime',
            'ngay_tra_link_in' => 'date',
            'ngay_hen_tra_hang' => 'date',
            'tong_tien' => 'decimal:2',
            'so_tien_giam_gia' => 'decimal:2',
            'thanh_toan_lan_1' => 'decimal:2',
            'thanh_toan_lan_2' => 'decimal:2',
            'thanh_toan_lan_3' => 'decimal:2',
        ];
    }

    /** Người tạo hợp đồng (user đăng nhập). */
    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id', 'id');
    }

    /** Thợ chụp (nhân viên). */
    public function thoChup(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'tho_chup_id', 'id');
    }

    /** Thợ make (nhân viên). */
    public function thoMake(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'tho_make_id', 'id');
    }

    /** Thợ edit (nhân viên). */
    public function thoEdit(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'tho_edit_id', 'id');
    }

    /**
     * Người chụp (user) theo cột id_nguoi_chup.
     *
     * Một số database đang lưu trực tiếp user_id của người chụp ở cột id_nguoi_chup
     * (khác với cấu trúc tho_chup_id -> nhan_vien -> user).
     */
    public function nguoiChupUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_nguoi_chup', 'id');
    }

    /** Trang phục thuê (hợp đồng thuê trang phục — cột trang_phuc_id). */
    public function trangPhuc(): BelongsTo
    {
        return $this->belongsTo(TrangPhuc::class, 'trang_phuc_id', 'id');
    }

    /** Người cho thuê (user đăng nhập khi tạo HĐ thuê trang phục — cột user_id). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /** Dịch vụ lẻ đã chọn trong hợp đồng (chỉ những dòng user tích chọn khi thêm/sửa). */
    public function dichVuLe(): BelongsToMany
    {
        return $this->belongsToMany(
            DichVuLe::class,
            'hop_dong_dich_vu_le',
            'hop_dong_id',
            'dich_vu_le_id'
        )
            ->using(HopDongDichVuLe::class)
            ->withPivot('gia_goc', 'gia_thuc')
            ->withTimestamps();
    }
}

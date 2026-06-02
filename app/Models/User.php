<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    /** 1=admin, 2=nhân viên, 3=người dùng */
    public const ROLE_ADMIN = 1;
    public const ROLE_NHAN_VIEN = 2;
    public const ROLE_NGUOI_DUNG = 3;

    public const SAP_XEP_ID = 'id';

    public const SAP_XEP_HO_TEN = 'ho_ten';

    public const SAP_XEP_NGAY_SINH = 'ngay_sinh';

    public const SAP_XEP_NGAY_VAO_CONG_TY = 'ngay_vao_cong_ty';

    public const SAP_XEP_NGAY_KY_HOP_DONG = 'ngay_ky_hop_dong';

    public const SAP_XEP_LUONG_CO_BAN = 'luong_co_ban';

    public const SAP_XEP_LUONG_TANG_CA = 'luong_tang_ca';

    public const SAP_XEP_VAI_TRO = 'vai_tro';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_ID;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_ID => 'Mới nhất',
        self::SAP_XEP_HO_TEN => 'Họ tên',
        self::SAP_XEP_NGAY_SINH => 'Ngày sinh',
        self::SAP_XEP_NGAY_VAO_CONG_TY => 'Ngày vào công ty',
        self::SAP_XEP_NGAY_KY_HOP_DONG => 'Ngày ký hợp đồng',
        self::SAP_XEP_LUONG_CO_BAN => 'Lương cơ bản',
        self::SAP_XEP_LUONG_TANG_CA => 'Lương tăng ca',
        self::SAP_XEP_VAI_TRO => 'Vai trò',
    ];

    /** @var array<string, string> */
    public const GIOI_TINH_OPTIONS = [
        'nam' => 'Nam',
        'nu' => 'Nữ',
        'khac' => 'Khác',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
        ];
    }

    /**
     * Nhãn vai trò hiển thị (tiếng Việt).
     */
    public function getRoleLabelAttribute(): string
    {
        return match ((int) $this->role) {
            self::ROLE_ADMIN => 'Quản trị viên',
            self::ROLE_NHAN_VIEN => 'Nhân viên',
            self::ROLE_NGUOI_DUNG => 'Người dùng',
            default => 'Người dùng',
        };
    }

    /**
     * Một user có thể có một hồ sơ nhân viên.
     */
    public function nhanVien(): HasOne
    {
        return $this->hasOne(NhanVien::class, 'user_id', 'id');
    }

    /**
     * Một user có nhiều bản ghi điểm danh.
     */
    public function diemDanh(): HasMany
    {
        return $this->hasMany(DiemDanh::class, 'user_id', 'id');
    }

    /**
     * Một user có nhiều bản ghi chấm công.
     */
    public function chamCong(): HasMany
    {
        return $this->hasMany(ChamCong::class, 'user_id', 'id');
    }

    /**
     * Một user có nhiều hợp đồng cho thuê trang phục.
     */
    public function hopDongChoThueTrangPhuc(): HasMany
    {
        return $this->hasMany(HopDongChoThueTrangPhuc::class, 'nguoi_cho_thue', 'id');
    }
}

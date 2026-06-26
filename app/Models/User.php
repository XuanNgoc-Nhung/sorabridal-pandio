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

    public const STATUS_DA_NGHI = 0;

    public const STATUS_DANG_LAM_VIEC = 1;

    public const STATUS_GIOI_HAN_QUYEN = 2;

    public const STATUS_MAC_DINH = self::STATUS_DANG_LAM_VIEC;

    /** @var array<int, string> */
    public const STATUS_OPTIONS = [
        self::STATUS_DANG_LAM_VIEC => 'Đang làm việc',
        self::STATUS_DA_NGHI => 'Đã nghỉ việc',
        self::STATUS_GIOI_HAN_QUYEN => 'Hạn chế quyền',
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
        if ($this->relationLoaded('vaiTro') && $this->vaiTro) {
            return $this->vaiTro->ten_vai_tro;
        }

        return VaiTro::tenChoMa($this->role) ?? '—';
    }

    public function isAdmin(): bool
    {
        return VaiTro::isAdminMa($this->role);
    }

    public function daNghiViec(): bool
    {
        return (int) $this->status === self::STATUS_DA_NGHI;
    }

    public function biGioiHanQuyenTruyCap(): bool
    {
        return (int) $this->status === self::STATUS_GIOI_HAN_QUYEN;
    }

    public function coTheDangNhap(): bool
    {
        return (int) ($this->status ?? self::STATUS_MAC_DINH) === self::STATUS_DANG_LAM_VIEC;
    }

    public function routeThongBaoTrangThai(): ?string
    {
        if ($this->daNghiViec()) {
            return 'auth.da-nghi-viec';
        }

        if ($this->biGioiHanQuyenTruyCap()) {
            return 'auth.gioi-han-quyen';
        }

        return null;
    }

    /**
     * Quyền điều chỉnh hợp đồng cưới (từ vai_tro.dieu_chinh_hop_dong_cuoi).
     */
    public function coQuyenDieuChinhHopDongCuoi(): bool
    {
        return (bool) ($this->vaiTro?->dieu_chinh_hop_dong_cuoi ?? false);
    }

    /**
     * Vai trò hệ thống (menu sidebar) — user.role khớp vai_tro.ma_vai_tro.
     */
    public function vaiTro(): HasOne
    {
        return $this->hasOne(VaiTro::class, 'ma_vai_tro', 'role');
    }

    /**
     * Danh sách route menu sidebar từ bảng vai_tro (theo user.role → ma_vai_tro).
     *
     * @return array<string>
     */
    public function sidebarDsMenuFromVaiTro(): array
    {
        if ($this->role === null) {
            return [];
        }

        $vaiTro = $this->relationLoaded('vaiTro')
            ? $this->vaiTro
            : VaiTro::query()->where('ma_vai_tro', (string) $this->role)->first();

        $menu = $vaiTro?->ds_menu;

        return is_array($menu) ? $menu : [];
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
     * Một user có nhiều đơn xin nghỉ phép.
     */
    public function xinNghiPhep(): HasMany
    {
        return $this->hasMany(XinNghiPhep::class, 'user_id', 'id');
    }

    /**
     * Các đơn xin nghỉ phép do user này duyệt.
     */
    public function nghiPhepDaDuyet(): HasMany
    {
        return $this->hasMany(XinNghiPhep::class, 'nguoi_duyet', 'id');
    }

    /**
     * Một user có nhiều hợp đồng cho thuê trang phục.
     */
    public function hopDongChoThueTrangPhuc(): HasMany
    {
        return $this->hasMany(HopDongChoThueTrangPhuc::class, 'nguoi_cho_thue', 'id');
    }
}

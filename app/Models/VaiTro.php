<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

class VaiTro extends Model
{
    use HasFactory;

    /** Mã vai trò quản trị (toàn quyền menu) — khớp users.role. */
    public const MA_ADMIN = '1';

    /** Mã vai trò nhân viên mặc định khi tạo tài khoản. */
    public const MA_NHAN_VIEN = '2';

    /** Mã vai trò người dùng / khách. */
    public const MA_NGUOI_DUNG = '3';

    public const SAP_XEP_ID = 'id';

    public const SAP_XEP_MA = 'ma_vai_tro';

    public const SAP_XEP_TEN = 'ten_vai_tro';

    public const SAP_XEP_USERS = 'users_count';

    public const SAP_XEP_MO_TA = 'mo_ta';

    public const SAP_XEP_GHI_CHU = 'ghi_chu';

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_ID;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_TEN => 'Tên vai trò',
        self::SAP_XEP_USERS => 'Số người dùng',
        self::SAP_XEP_MO_TA => 'Mô tả',
        self::SAP_XEP_GHI_CHU => 'Ghi chú',
    ];

    protected $table = 'vai_tro';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ma_vai_tro',
        'ten_vai_tro',
        'mo_ta',
        'ghi_chu',
        'ds_menu',
        'dieu_chinh_hop_dong_cuoi',
    ];

    protected function casts(): array
    {
        return [
            'ds_menu' => 'array',
            'dieu_chinh_hop_dong_cuoi' => 'boolean',
        ];
    }

    /**
     * Tài khoản có user.role khớp ma_vai_tro.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'ma_vai_tro');
    }

    /**
     * @return list<string>
     */
    public static function danhSachMaHopLe(): array
    {
        return static::query()
            ->orderBy('ma_vai_tro')
            ->pluck('ma_vai_tro')
            ->all();
    }

    public static function tenChoMa(mixed $ma): ?string
    {
        if ($ma === null || $ma === '') {
            return null;
        }

        return static::query()
            ->where('ma_vai_tro', (string) $ma)
            ->value('ten_vai_tro');
    }

    public static function maMacDinhNhanVien(): string
    {
        if (static::query()->where('ma_vai_tro', self::MA_NHAN_VIEN)->exists()) {
            return self::MA_NHAN_VIEN;
        }

        $ma = static::query()
            ->where('ma_vai_tro', '!=', self::MA_ADMIN)
            ->orderBy('ma_vai_tro')
            ->value('ma_vai_tro');

        return $ma ?? self::MA_NHAN_VIEN;
    }

    public static function isAdminMa(mixed $role): bool
    {
        return (string) $role === self::MA_ADMIN;
    }

    /**
     * View tổng quan theo mã vai trò (dùng khi cần map tường minh).
     */
    public static function viewTongQuanChoMa(mixed $ma): string
    {
        return match ((string) $ma) {
            self::MA_ADMIN => 'admin.tong-quan-admin',
            self::MA_NHAN_VIEN => 'admin.tong-quan-nhan-vien',
            default => 'admin.tong-quan-mac-dinh',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function quyTacValidateRole(): array
    {
        return ['nullable', Rule::exists('vai_tro', 'ma_vai_tro')];
    }
}

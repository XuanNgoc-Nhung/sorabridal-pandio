<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhongBan extends Model
{
    use HasFactory;

    /** Mã phòng ban dùng khi điều phối hợp đồng cưới. */
    public const MA_CHUP = 'CS01';

    public const MA_MAKE = 'MS01';

    public const MA_EDIT = 'PS01';

    /** @var list<string> */
    public const MA_DIEU_PHOI_HOP_DONG = [
        self::MA_CHUP,
        self::MA_MAKE,
        self::MA_EDIT,
    ];

    public const SAP_XEP_ID = 'id';

    public const SAP_XEP_NHAN_VIENS = 'nhan_viens_count';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_ID;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_NHAN_VIENS => 'Số lượng nhân viên',
    ];

    protected $table = 'phong_ban';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ten_phong_ban',
        'ma_phong_ban',
        'mo_ta',
        'ghi_chu',
    ];

    /**
     * Danh sách nhân viên thuộc phòng ban (liên kết qua nhan_vien.phong_ban = ma_phong_ban).
     */
    public function nhanViens(): HasMany
    {
        return $this->hasMany(NhanVien::class, 'phong_ban', 'ma_phong_ban');
    }

    public static function maFromId(int $id): ?string
    {
        return static::query()->whereKey($id)->value('ma_phong_ban');
    }

    /**
     * Danh sách dịch vụ lẻ thuộc phòng ban này.
     *
     * Lưu ý: `dich_vu_le.phong_ban_id` là chuỗi id phân tách bằng dấu phẩy, ví dụ "1,2,5".
     */
    public function dichVuLes(): HasMany
    {
        return $this->hasMany(DichVuLe::class, 'phong_ban_id')
            ->whereRaw('FIND_IN_SET(?, phong_ban_id)', [$this->getKey()]);
    }
}


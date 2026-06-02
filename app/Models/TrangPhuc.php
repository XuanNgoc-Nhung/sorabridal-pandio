<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrangPhuc extends Model
{
    use HasFactory;

    protected $table = 'trang_phuc';

    /** Trạng thái: hiển thị */
    public const TRANG_THAI_ACTIVE = 1;

    /** Trạng thái: ẩn */
    public const TRANG_THAI_INACTIVE = 0;

    public const SAP_XEP_ID = 'id';

    public const SAP_XEP_TEN = 'ten_san_pham';

    public const SAP_XEP_MA = 'ma_san_pham';

    public const SAP_XEP_GIA_TRI = 'gia_tri';

    public const SAP_XEP_TRANG_THAI = 'trang_thai';

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_ID;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_ID => 'Mới nhất',
        self::SAP_XEP_TEN => 'Tên sản phẩm',
        self::SAP_XEP_MA => 'Mã sản phẩm',
        self::SAP_XEP_GIA_TRI => 'Giá trị',
        self::SAP_XEP_TRANG_THAI => 'Trạng thái',
        self::SAP_XEP_CREATED_AT => 'Ngày tạo',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ten_san_pham',
        'ma_san_pham',
        'slug',
        'hinh_anh',
        'mo_ta',
        'ghi_chu',
        'trang_thai',
        'gia_tri',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gia_tri' => 'decimal:2',
            'trang_thai' => 'integer',
        ];
    }

    /**
     * Sản phẩm được dùng trong nhiều dòng cho thuê.
     */
    public function sanPhamChoThue(): HasMany
    {
        return $this->hasMany(SanPhamChoThue::class, 'san_pham_id', 'id');
    }
}

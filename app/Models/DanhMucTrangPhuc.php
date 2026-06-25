<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DanhMucTrangPhuc extends Model
{
    use HasFactory;

    protected $table = 'danh_muc_trang_phuc';

    public const SAP_XEP_ID = 'id';

    public const SAP_XEP_TEN = 'ten_danh_muc';

    public const SAP_XEP_MA = 'ma_danh_muc';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_ID;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_ID => 'Mới nhất',
        self::SAP_XEP_TEN => 'Tên danh mục',
        self::SAP_XEP_MA => 'Mã danh mục',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ma_danh_muc',
        'ten_danh_muc',
        'ghi_chu',
    ];

    public function trangPhucs(): HasMany
    {
        return $this->hasMany(TrangPhuc::class, 'loai_trang_phuc');
    }
}

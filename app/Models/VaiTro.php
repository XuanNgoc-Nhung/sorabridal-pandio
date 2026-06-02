<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaiTro extends Model
{
    use HasFactory;

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
    ];

    protected function casts(): array
    {
        return [
            'ds_menu' => 'array',
        ];
    }

    /**
     * Tài khoản có user.role khớp ma_vai_tro.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'ma_vai_tro');
    }
}

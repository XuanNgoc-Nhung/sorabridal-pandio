<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaLamViec extends Model
{
    use HasFactory;

    protected $table = 'ca_lam_viec';

    public const SAP_XEP_TEN = 'ten_ca';

    public const SAP_XEP_GIO_BAT_DAU = 'gio_bat_dau';

    public const SAP_XEP_GIO_KET_THUC = 'gio_ket_thuc';

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_GIO_BAT_DAU;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_TEN => 'Tên ca',
        self::SAP_XEP_GIO_BAT_DAU => 'Giờ bắt đầu',
        self::SAP_XEP_GIO_KET_THUC => 'Giờ kết thúc',
        self::SAP_XEP_CREATED_AT => 'Thời gian tạo',
    ];

    /** @var list<string> */
    protected $fillable = [
        'ten_ca',
        'gio_bat_dau',
        'gio_ket_thuc',
    ];

    public function gioBatDauHienThi(): string
    {
        return self::formatGio($this->gio_bat_dau);
    }

    public function gioKetThucHienThi(): string
    {
        return self::formatGio($this->gio_ket_thuc);
    }

    public static function formatGio(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return substr((string) $value, 0, 5);
    }
}

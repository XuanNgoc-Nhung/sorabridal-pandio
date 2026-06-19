<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpDiemDanh extends Model
{
    use HasFactory;

    protected $table = 'ip_diem_danh';

    public const TRANG_THAI_NGUNG_HOAT_DONG = 0;

    public const TRANG_THAI_DANG_HOAT_DONG = 1;

    public const SAP_XEP_TEN = 'ten_ip';

    public const SAP_XEP_DIA_CHI_IP = 'dia_chi_ip';

    public const SAP_XEP_TRANG_THAI = 'trang_thai';

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_CREATED_AT;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_TEN => 'Tên IP',
        self::SAP_XEP_DIA_CHI_IP => 'Địa chỉ IP',
        self::SAP_XEP_TRANG_THAI => 'Cho phép điểm danh',
        self::SAP_XEP_CREATED_AT => 'Thời gian tạo',
    ];

    /** @var array<int, string> */
    public const TRANG_THAI_LABELS = [
        self::TRANG_THAI_DANG_HOAT_DONG => 'Cho phép',
        self::TRANG_THAI_NGUNG_HOAT_DONG => 'Không cho phép',
    ];

    /** @var array<int, string> */
    public const TRANG_THAI_BADGE_CLASSES = [
        self::TRANG_THAI_DANG_HOAT_DONG => 'bg-label-success',
        self::TRANG_THAI_NGUNG_HOAT_DONG => 'bg-label-secondary',
    ];

    protected $fillable = [
        'ten_ip',
        'dia_chi_ip',
        'ghi_chu',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'trang_thai' => 'integer',
        ];
    }

    public function trangThaiLabel(): string
    {
        return self::TRANG_THAI_LABELS[$this->trang_thai] ?? '—';
    }

    public function trangThaiBadgeClass(): string
    {
        return self::TRANG_THAI_BADGE_CLASSES[$this->trang_thai] ?? 'bg-label-secondary';
    }

    /**
     * Danh sách IP được phép điểm danh: ten_ip => dia_chi_ip.
     *
     * @return array<string, string>
     */
    public static function diaChiIpAllowlistDangHoatDong(): array
    {
        $allowlist = [];

        foreach (self::query()
            ->where('trang_thai', self::TRANG_THAI_DANG_HOAT_DONG)
            ->get(['ten_ip', 'dia_chi_ip']) as $row) {
            $ip = trim((string) $row->dia_chi_ip);
            if ($ip !== '') {
                $allowlist[(string) $row->ten_ip] = $ip;
            }
        }

        return $allowlist;
    }
}

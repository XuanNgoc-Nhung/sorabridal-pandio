<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class HopDongChoThueTrangPhuc extends Model
{
    use HasFactory;

    public const SAP_XEP_ID = 'id';

    public const SAP_XEP_TEN_KHACH = 'ten_khach_hang';

    public const SAP_XEP_SDT = 'sdt_khach_hang';

    public const SAP_XEP_SO_NGAY_THUE = 'so_ngay_thue';

    public const SAP_XEP_TONG_TIEN = 'tong_tien';

    public const SAP_XEP_NGAY_THUE = 'ngay_thue';

    public const SAP_XEP_NGAY_TRA_DU_KIEN = 'ngay_tra_du_kien';

    public const SAP_XEP_NGAY_TRA_CHINH_THUC = 'ngay_tra_chinh_thuc';

    public const SAP_XEP_TRANG_THAI = 'trang_thai';

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_ID;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_ID => 'Mới nhất',
        self::SAP_XEP_TEN_KHACH => 'Tên khách hàng',
        self::SAP_XEP_SDT => 'Số điện thoại',
        self::SAP_XEP_SO_NGAY_THUE => 'Số ngày thuê',
        self::SAP_XEP_TONG_TIEN => 'Tiền thuê',
        self::SAP_XEP_NGAY_THUE => 'Ngày bắt đầu thuê',
        self::SAP_XEP_NGAY_TRA_DU_KIEN => 'Ngày trả dự kiến',
        self::SAP_XEP_NGAY_TRA_CHINH_THUC => 'Ngày trả chính thức',
        self::SAP_XEP_TRANG_THAI => 'Trạng thái',
        self::SAP_XEP_CREATED_AT => 'Ngày tạo',
    ];

    protected $table = 'hop_dong_cho_thue_trang_phuc';

    protected static function booted(): void
    {
        static::saving(function (HopDongChoThueTrangPhuc $model): void {
            $model->so_ngay_thue = static::tinhSoNgayThue(
                $model->ngay_thue,
                $model->ngay_tra_du_kien,
                $model->ngay_tra_chinh_thuc,
            );
        });
    }

    /**
     * Số ngày thuê (tính cả ngày bắt đầu và ngày kết thúc). Kết thúc = ngày trả chính thức nếu có, không thì ngày trả dự kiến.
     */
    public static function tinhSoNgayThue(mixed $ngayThue, mixed $ngayTraDuKien, mixed $ngayTraChinhThuc = null): int
    {
        if ($ngayThue === null || $ngayTraDuKien === null) {
            return 1;
        }

        $start = Carbon::parse($ngayThue)->startOfDay();
        $end = Carbon::parse($ngayTraChinhThuc ?? $ngayTraDuKien)->startOfDay();

        if ($end->lt($start)) {
            return 1;
        }

        return max(1, (int) $start->diffInDays($end) + 1);
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ten_khach_hang',
        'sdt_khach_hang',
        'ngay_thue',
        'ngay_tra_du_kien',
        'ngay_tra_chinh_thuc',
        'tong_tien',
        'tien_coc',
        'trang_thai',
        'ghi_chu',
        'nguoi_cho_thue',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'so_ngay_thue' => 'integer',
            'ngay_thue' => 'date',
            'ngay_tra_du_kien' => 'date',
            'ngay_tra_chinh_thuc' => 'date',
            'tong_tien' => 'decimal:2',
            'tien_coc' => 'decimal:2',
            'trang_thai' => 'integer',
            'nguoi_cho_thue' => 'integer',
        ];
    }

    public function nguoiChoThue(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_cho_thue', 'id');
    }

    public function sanPhamChoThue(): HasMany
    {
        return $this->hasMany(SanPhamChoThue::class, 'hop_dong_id', 'id');
    }
}

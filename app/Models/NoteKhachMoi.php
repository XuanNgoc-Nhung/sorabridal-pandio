<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NoteKhachMoi extends Model
{
    use HasFactory;

    public const TRANG_THAI_DANG_TU_VAN = 'dang_tu_van';

    public const TRANG_THAI_CO_ADD_ZALO = 'co_add_zalo';

    public const TRANG_THAI_DA_HEN_LICH = 'da_hen_lich';

    public const TRANG_THAI_DA_DEN = 'da_den';

    public const TRANG_THAI_DA_CHOT = 'da_chot';

    public const TRANG_THAI_KHONG_CHOT = 'khong_chot';

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_NGAY_HEN_LICH = 'ngay_hen_lich';

    public const SAP_XEP_NGAY_DEN_THUC_TE = 'ngay_den_thuc_te';

    public const SAP_XEP_TEN_KHACH = 'ten_khach';

    public const SAP_XEP_SO_DIEN_THOAI = 'so_dien_thoai';

    public const SAP_XEP_NGUON_KHACH = 'nguon_khach';

    public const SAP_XEP_TRANG_THAI = 'trang_thai';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_CREATED_AT;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_CREATED_AT => 'Ngày tạo',
        self::SAP_XEP_NGAY_HEN_LICH => 'Ngày hẹn lịch',
        self::SAP_XEP_NGAY_DEN_THUC_TE => 'Ngày đến thực tế',
        self::SAP_XEP_TEN_KHACH => 'Tên khách',
        self::SAP_XEP_SO_DIEN_THOAI => 'Số điện thoại',
        self::SAP_XEP_NGUON_KHACH => 'Nguồn khách',
        self::SAP_XEP_TRANG_THAI => 'Trạng thái',
    ];

    protected $table = 'note_khach_moi';

    protected $fillable = [
        'ten_khach',
        'so_dien_thoai',
        'phu_trach_sale_id',
        'ngay_hen_lich',
        'ngay_den_thuc_te',
        'nguon_khach',
        'nguoi_tao_id',
        'trang_thai',
        'ly_do_khong_chot',
    ];

    protected $casts = [
        'ngay_hen_lich' => 'datetime',
        'ngay_den_thuc_te' => 'date',
    ];

    /**
     * @return array<string, string>
     */
    public static function trangThaiLabels(): array
    {
        return [
            self::TRANG_THAI_DANG_TU_VAN => 'Đang tư vấn',
            self::TRANG_THAI_CO_ADD_ZALO => 'Có add zalo',
            self::TRANG_THAI_DA_HEN_LICH => 'Đã hẹn lịch',
            self::TRANG_THAI_DA_DEN => 'Đã đến',
            self::TRANG_THAI_DA_CHOT => 'Đã chốt',
            self::TRANG_THAI_KHONG_CHOT => 'Không chốt',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function trangThaiBadgeClasses(): array
    {
        return [
            self::TRANG_THAI_DANG_TU_VAN => 'bg-label-secondary',
            self::TRANG_THAI_CO_ADD_ZALO => 'bg-label-primary',
            self::TRANG_THAI_DA_HEN_LICH => 'bg-label-warning',
            self::TRANG_THAI_DA_DEN => 'bg-label-info',
            self::TRANG_THAI_DA_CHOT => 'bg-label-success',
            self::TRANG_THAI_KHONG_CHOT => 'bg-label-danger',
        ];
    }

    /**
     * @return list<string>
     */
    public static function trangThaiCanLyDoKhongChot(): array
    {
        return [
            self::TRANG_THAI_KHONG_CHOT,
            self::TRANG_THAI_CO_ADD_ZALO,
        ];
    }

    public static function formatNgayCoThu(?CarbonInterface $date): string
    {
        if ($date === null) {
            return '—';
        }

        return self::thuTrongTuanText($date).' '.$date->format('d/m/Y');
    }

    public static function formatNgayGioCoThu(?CarbonInterface $date): string
    {
        if ($date === null) {
            return '—';
        }

        return self::thuTrongTuanText($date).' '.$date->format('d/m/Y H:i');
    }

    public static function thuTrongTuanText(?CarbonInterface $date): string
    {
        if ($date === null) {
            return '—';
        }

        return self::thuTrongTuan($date);
    }

    private static function thuTrongTuan(CarbonInterface $date): string
    {
        $thu = [
            1 => 'Thứ hai',
            2 => 'Thứ ba',
            3 => 'Thứ tư',
            4 => 'Thứ năm',
            5 => 'Thứ sáu',
            6 => 'Thứ bảy',
            7 => 'Chủ nhật',
        ];

        return $thu[$date->isoWeekday()];
    }

    public function phuTrachSale(): BelongsTo
    {
        return $this->belongsTo(User::class, 'phu_trach_sale_id');
    }

    public function phuTrachSales(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'note_khach_moi_phu_trach_sale', 'note_khach_moi_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * @return list<int>
     */
    public function phuTrachSaleNhanVienIds(): array
    {
        $userIds = $this->phuTrachSales->pluck('id')->filter()->values();
        if ($userIds->isEmpty() && $this->phu_trach_sale_id) {
            $userIds = collect([$this->phu_trach_sale_id]);
        }

        if ($userIds->isEmpty()) {
            return [];
        }

        return NhanVien::query()
            ->whereIn('user_id', $userIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    /**
     * HĐ cưới khớp SĐT note với SĐT cô dâu/chú rể (lấy HĐ mới nhất nếu có nhiều).
     */
    public function hopDongCuoiKhop(): ?HopDongCuoi
    {
        return HopDongCuoi::findByContactPhone($this->so_dien_thoai);
    }

    /**
     * @return array{ma_hop_dong: string|null, hinh_thuc_coc: string|null}
     */
    public function traCuuHopDong(): array
    {
        return HopDongCuoi::traCuuTheoSoDienThoai($this->so_dien_thoai);
    }

    /**
     * @param  array<string, array{ma_hop_dong: string|null, hinh_thuc_coc: string|null}>  $map
     * @return array{ma_hop_dong: string|null, hinh_thuc_coc: string|null}
     */
    public function traCuuHopDongTuMap(array $map): array
    {
        $normalized = HopDongCuoi::normalizeContactPhone($this->so_dien_thoai);
        if ($normalized === null) {
            return ['ma_hop_dong' => null, 'hinh_thuc_coc' => null];
        }

        return $map[$normalized] ?? ['ma_hop_dong' => null, 'hinh_thuc_coc' => null];
    }

    /**
     * @param  iterable<mixed>  $phones
     * @return array<string, array{ma_hop_dong: string|null, hinh_thuc_coc: string|null}>
     */
    public static function mapTraCuuHopDongTheoSoDienThoai(iterable $phones): array
    {
        return HopDongCuoi::mapTraCuuTheoSoDienThoai($phones);
    }

    public function getTrangThaiLabelAttribute(): string
    {
        if ($this->trang_thai === null || $this->trang_thai === '') {
            return '—';
        }

        return self::trangThaiLabels()[$this->trang_thai] ?? $this->trang_thai;
    }

    public function getTrangThaiBadgeClassAttribute(): string
    {
        if ($this->trang_thai === null || $this->trang_thai === '') {
            return 'bg-label-secondary';
        }

        return self::trangThaiBadgeClasses()[$this->trang_thai] ?? 'bg-label-secondary';
    }
}

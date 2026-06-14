<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HopDongCuoi extends Model
{
    protected $table = 'hop_dong_cuoi';

    /** @var list<string> Giá trị ENUM cột trang_thai_hop_dong */
    public const TRANG_THAI_HOP_DONG = [
        'nhap',
        'da_huy',
        'dang_thuc_hien',
        'tre_chup',
        'tre_edit',
    ];

    /** Trạng thái hiển thị trong filter danh sách HĐ (ẩn nháp). */
    public const TRANG_THAI_HOP_DONG_HIEN_THI_DSACH = [
        'da_huy',
        'dang_thuc_hien',
        'tre_chup',
        'tre_edit',
    ];

    public const HINH_THUC_COC_TAI_CUA_HANG = 'tai_cua_hang';

    public const HINH_THUC_COC_ONLINE = 'online';

    /** @var array<string, string> Giá trị => nhãn hiển thị cột hinh_thuc_coc */
    public const HINH_THUC_COC = [
        self::HINH_THUC_COC_TAI_CUA_HANG => 'Tại cửa hàng',
        self::HINH_THUC_COC_ONLINE => 'Online',
    ];

    /** @var array<string, string> Giá trị => nhãn hiển thị cột loai_hop_dong */
    public const LOAI_HOP_DONG = [
        'pre_wedding' => 'Pre-wedding',
        'phong_su_cuoi' => 'Phóng sự cưới',
    ];

    public const SAP_XEP_ID = 'id';

    public const SAP_XEP_TEN_CO_DAU = 'ten_co_dau';

    public const SAP_XEP_TEN_CHU_RE = 'ten_chu_re';

    public const SAP_XEP_NGAY_CUOI = 'ngay_cuoi';

    public const SAP_XEP_NGAY_CHUP = 'ngay_chup';

    public const SAP_XEP_TIEN_DO_THANH_TOAN = 'tien_do_thanh_toan';

    public const SAP_XEP_MA_HOP_DONG = 'ma_hop_dong';

    public const SAP_XEP_TONG_TIEN = 'tong_tien';

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_ID;

    /** @var array<string, string> Tiêu chí sắp xếp danh sách HĐ cưới */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_ID => 'Mới nhất',
        self::SAP_XEP_TEN_CO_DAU => 'Tên cô dâu',
        self::SAP_XEP_TEN_CHU_RE => 'Tên chú rể',
        self::SAP_XEP_NGAY_CUOI => 'Thời gian cưới',
        self::SAP_XEP_NGAY_CHUP => 'Thời gian chụp',
        self::SAP_XEP_TIEN_DO_THANH_TOAN => 'Tiến độ thanh toán',
        self::SAP_XEP_MA_HOP_DONG => 'Mã hợp đồng',
        self::SAP_XEP_TONG_TIEN => 'Tổng tiền',
        self::SAP_XEP_CREATED_AT => 'Ngày tạo',
    ];

    protected $fillable = [
        'ma_hop_dong',
        'loai_hop_dong',
        'ten_co_dau',
        'ten_chu_re',
        'email_sdt_co_dau',
        'email_sdt_chu_re',
        'ngay_chup_du_kien',
        'ngay_chup_thuc_te',
        'gio_chup',
        'buoi_chup',
        'ngay_cuoi_du_kien',
        'ngay_cuoi_chinh_thuc',
        'dia_diem_chup',
        'concept_id',
        'loai_dich_vu',
        'nhom_dich_vu_id',
        'kenh_tiep_can',
        'yeu_cau_dac_biet',
        'tong_tien',
        'chiet_khau',
        'tien_coc',
        'hinh_thuc_coc',
        'trang_thai_hop_dong',
        'link_demo',
        'ngay_tra_link_demo_du_kien',
        'ngay_tra_link_demo_chinh_thuc',
        'ngay_up_link_demo_gan_nhat',
        'nguoi_up_link_demo_id',
        'link_in',
        'ngay_tra_link_in_du_kien',
        'ngay_tra_link_in_chinh_thuc',
        'ngay_up_link_in_gan_nhat',
        'nguoi_up_link_in_id',
        'ghi_chu_sale',
        'tho_chup_id',
        'tho_make_id',
        'tho_edit_id',
        'ngay_ky_hop_dong',
        'han_thanh_toan_lan2',
        'han_thanh_toan_lan3',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'ngay_chup_du_kien' => 'date',
            'ngay_chup_thuc_te' => 'date',
            'ngay_cuoi_du_kien' => 'date',
            'ngay_cuoi_chinh_thuc' => 'date',
            'ngay_tra_link_demo_du_kien' => 'date',
            'ngay_tra_link_demo_chinh_thuc' => 'date',
            'ngay_up_link_demo_gan_nhat' => 'datetime',
            'ngay_tra_link_in_du_kien' => 'date',
            'ngay_tra_link_in_chinh_thuc' => 'date',
            'ngay_up_link_in_gan_nhat' => 'datetime',
            'ngay_ky_hop_dong' => 'date',
            'han_thanh_toan_lan2' => 'date',
            'han_thanh_toan_lan3' => 'date',
            'tong_tien' => 'decimal:2',
            'chiet_khau' => 'decimal:2',
            'tien_coc' => 'decimal:2',
            'nhom_dich_vu_id' => 'integer',
            'tho_chup_id' => 'integer',
            'tho_make_id' => 'integer',
            'tho_edit_id' => 'integer',
        ];
    }

    public function concept(): BelongsTo
    {
        return $this->belongsTo(Concept::class, 'concept_id');
    }

    public function nhomDichVu(): BelongsTo
    {
        return $this->belongsTo(NhomDichVu::class, 'nhom_dich_vu_id');
    }

    public function hopDongCuoiNhomDichVu(): HasMany
    {
        return $this->hasMany(HopDongCuoiNhomDichVu::class, 'hop_dong_cuoi_id');
    }

    public function hopDongCuoiDichVuLe(): HasMany
    {
        return $this->hasMany(HopDongCuoiDichVuLe::class, 'hop_dong_cuoi_id');
    }

    public function hopDongCuoiTrangPhuc(): HasMany
    {
        return $this->hasMany(HopDongCuoiTrangPhuc::class, 'hop_dong_cuoi_id');
    }

    public function hopDongThanhToan(): HasMany
    {
        return $this->hasMany(HopDongThanhToan::class, 'hop_dong_id');
    }

    public function thanhVienHopDongCuis(): HasMany
    {
        return $this->hasMany(ThanhVienHopDongCuoi::class, 'hop_dong_id');
    }

    /**
     * @return array<string, string>
     */
    public static function kenhTiepCanLabels(): array
    {
        return [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'zalo' => 'Zalo',
            'google' => 'Google / tìm kiếm',
            'gioi_thieu' => 'Giới thiệu',
            'khac' => 'Khác',
        ];
    }

    public static function nguonKhachTuKenhTiepCan(?string $kenh): ?string
    {
        if ($kenh === null || $kenh === '') {
            return null;
        }

        return self::kenhTiepCanLabels()[$kenh] ?? $kenh;
    }

    /**
     * Chuẩn hóa giá trị nguồn khách từ form (key kênh, nhãn đã lưu, hoặc text cũ).
     */
    public static function normalizeNguonKhachInput(?string $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        if (array_key_exists($value, self::kenhTiepCanLabels())) {
            return self::nguonKhachTuKenhTiepCan($value);
        }

        foreach (self::kenhTiepCanLabels() as $label) {
            if ($value === $label) {
                return $label;
            }
        }

        return $value;
    }

    /**
     * Map giá trị đã lưu (key hoặc nhãn) về key kênh; null nếu là text cũ không khớp danh mục.
     */
    public static function nguonKhachToKenhKey(?string $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        if (array_key_exists($value, self::kenhTiepCanLabels())) {
            return $value;
        }

        foreach (self::kenhTiepCanLabels() as $key => $label) {
            if ($value === $label || mb_strtolower($value) === mb_strtolower($label)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Tên hiển thị: tên cuối chú rể - tên cuối cô dâu (bỏ tên đệm).
     */
    public static function formatTenKhachTuHopDong(?string $tenChuRe, ?string $tenCoDau): ?string
    {
        $tenCuoi = static function (?string $hoTen): string {
            $hoTen = trim((string) $hoTen);
            if ($hoTen === '') {
                return '';
            }
            $parts = preg_split('/\s+/u', $hoTen) ?: [];

            return $parts !== [] ? (string) end($parts) : '';
        };

        $chuRe = $tenCuoi($tenChuRe);
        $coDau = $tenCuoi($tenCoDau);

        if ($chuRe === '' && $coDau === '') {
            return null;
        }

        if ($chuRe === '') {
            return $coDau;
        }

        if ($coDau === '') {
            return $chuRe;
        }

        return $chuRe.' - '.$coDau;
    }

    /**
     * Tổng tiền hợp đồng (cột tong_tien) — dùng cho modal / tổng cần quy đổi.
     */
    public function tongPhaiThu(): float
    {
        return max(0, (float) ($this->tong_tien ?? 0));
    }

    /**
     * Đã thanh toán theo cột tien_coc (được cộng khi ghi nhận qua modal).
     */
    public function tongDaThanhToan(): float
    {
        return max(0, (float) ($this->tien_coc ?? 0));
    }

    /** Còn phải thanh toán = tong_tien - tien_coc */
    public function soTienConLai(): float
    {
        return max(0, $this->tongPhaiThu() - $this->tongDaThanhToan());
    }

    public function thoChup(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'tho_chup_id');
    }

    public function thoMake(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'tho_make_id');
    }

    public function thoEdit(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'tho_edit_id');
    }

    /**
     * Chuẩn hóa SĐT (0xxxxxxxxx) từ chuỗi liên hệ; null nếu không trích được số hợp lệ.
     */
    public static function normalizeContactPhone(mixed $value): ?string
    {
        $raw = trim((string) ($value ?? ''));
        if ($raw === '') {
            return null;
        }

        $candidates = preg_split('/[\r\n,;|]+/u', $raw) ?: [$raw];
        foreach ($candidates as $cand) {
            $cand = trim((string) $cand);
            if ($cand === '') {
                continue;
            }

            if (str_contains($cand, '@') && ! preg_match('/\d/', $cand)) {
                continue;
            }

            $clean = preg_replace('/[^\d+]+/u', '', $cand);
            $clean = $clean !== null ? $clean : '';
            $clean = ltrim($clean);
            if ($clean === '') {
                continue;
            }

            if ($clean[0] === '+') {
                $clean = '+'.str_replace('+', '', substr($clean, 1));
            } else {
                $clean = str_replace('+', '', $clean);
            }

            if (str_starts_with($clean, '+84')) {
                $rest = substr($clean, 3);
                if ($rest !== '' && $rest[0] !== '0') {
                    $clean = '0'.$rest;
                }
            }

            $digits = preg_replace('/\D+/', '', $clean) ?? '';
            if ($digits === '') {
                continue;
            }

            if (str_starts_with($digits, '84') && strlen($digits) >= 11) {
                $digits = '0'.substr($digits, 2);
            }

            if (strlen($digits) < 9 || strlen($digits) > 11) {
                continue;
            }

            if (! str_starts_with($digits, '0')) {
                continue;
            }

            return $digits;
        }

        return null;
    }

    /**
     * HĐ cuối mới nhất khớp SĐT chú rể hoặc cô dâu.
     */
    public static function findByContactPhone(mixed $phone): ?self
    {
        $normalized = self::normalizeContactPhone($phone);
        if ($normalized === null) {
            return null;
        }

        return self::query()
            ->with(['thanhVienHopDongCuis'])
            ->where(function ($q) use ($normalized) {
                $q->where('email_sdt_chu_re', $normalized)
                    ->orWhere('email_sdt_co_dau', $normalized);
            })
            ->orderByDesc('id')
            ->first();
    }

    /**
     * @return array{
     *     ma_hop_dong: string|null,
     *     ten_khach: string|null,
     *     kenh_tiep_can: string|null,
     *     phu_trach_sale_nhan_vien_ids: list<int>
     * }
     */
    public static function lookupPayloadByContactPhone(mixed $phone): array
    {
        $empty = [
            'ma_hop_dong' => null,
            'ten_khach' => null,
            'kenh_tiep_can' => null,
            'phu_trach_sale_nhan_vien_ids' => [],
        ];

        $hopDong = self::findByContactPhone($phone);
        if ($hopDong === null) {
            return $empty;
        }

        $saleNhanVienIds = $hopDong->thanhVienHopDongCuis
            ->pluck('nhan_vien_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        return [
            'ma_hop_dong' => $hopDong->ma_hop_dong,
            'ten_khach' => self::formatTenKhachTuHopDong($hopDong->ten_chu_re, $hopDong->ten_co_dau),
            'kenh_tiep_can' => $hopDong->kenh_tiep_can,
            'phu_trach_sale_nhan_vien_ids' => $saleNhanVienIds,
        ];
    }
}

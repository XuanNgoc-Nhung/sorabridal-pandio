<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class XinNghiPhep extends Model
{
    use HasFactory;

    protected $table = 'xin_nghi_phep';

    public const LOAI_DI_MUON = 'di_muon';

    public const LOAI_VE_SOM = 've_som';

    public const LOAI_NUA_NGAY = 'nua_ngay';

    public const LOAI_CA_NGAY = 'ca_ngay';

    public const LOAI_NHIEU_NGAY = 'nhieu_ngay';

    /** @var array<string, string> */
    public const LOAI_NGHI_PHEP_OPTIONS = [
        self::LOAI_DI_MUON => 'Đi muộn',
        self::LOAI_VE_SOM => 'Về sớm',
        self::LOAI_NUA_NGAY => 'Nửa ngày',
        self::LOAI_CA_NGAY => 'Cả ngày',
        self::LOAI_NHIEU_NGAY => 'Nhiều ngày',
    ];

    public const BUOI_SANG = 'sang';

    public const BUOI_CHIEU = 'chieu';

    /** @var array<string, string> */
    public const BUOI_NGHI_OPTIONS = [
        self::BUOI_SANG => 'Sáng',
        self::BUOI_CHIEU => 'Chiều',
    ];

    public const TRANG_THAI_CHO_DUYET = 'cho_duyet';

    public const TRANG_THAI_DA_DUYET = 'da_duyet';

    public const TRANG_THAI_TU_CHOI = 'tu_choi';

    /** @var array<string, string> */
    public const TRANG_THAI_OPTIONS = [
        self::TRANG_THAI_CHO_DUYET => 'Chờ duyệt',
        self::TRANG_THAI_DA_DUYET => 'Đã duyệt',
        self::TRANG_THAI_TU_CHOI => 'Từ chối',
    ];

    /** @var array<string, string> */
    public const TRANG_THAI_BADGE_CLASSES = [
        self::TRANG_THAI_CHO_DUYET => 'bg-label-warning',
        self::TRANG_THAI_DA_DUYET => 'bg-label-success',
        self::TRANG_THAI_TU_CHOI => 'bg-label-danger',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'loai_nghi_phep',
        'buoi_nghi',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'ly_do',
        'trang_thai',
        'nguoi_duyet',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ngay_bat_dau' => 'date',
            'ngay_ket_thuc' => 'date',
        ];
    }

    public function loaiNghiPhepLabel(): string
    {
        return self::LOAI_NGHI_PHEP_OPTIONS[$this->loai_nghi_phep] ?? '—';
    }

    public function buoiNghiLabel(): string
    {
        if ($this->buoi_nghi === null) {
            return '—';
        }

        return self::BUOI_NGHI_OPTIONS[$this->buoi_nghi] ?? '—';
    }

    public function trangThaiLabel(): string
    {
        return self::TRANG_THAI_OPTIONS[$this->trang_thai] ?? '—';
    }

    public function trangThaiBadgeClass(): string
    {
        return self::TRANG_THAI_BADGE_CLASSES[$this->trang_thai] ?? 'bg-label-secondary';
    }

    public function coTheXoaBoiChuDon(): bool
    {
        return in_array($this->trang_thai, [
            self::TRANG_THAI_CHO_DUYET,
            self::TRANG_THAI_TU_CHOI,
        ], true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function nguoiDuyet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_duyet', 'id');
    }

    /**
     * Các ngày áp dụng của đơn (một ngày hoặc khoảng ngày với loại nhiều ngày).
     *
     * @return list<\Illuminate\Support\Carbon>
     */
    public function cacNgayApDung(): array
    {
        if ($this->loai_nghi_phep === self::LOAI_NHIEU_NGAY && $this->ngay_ket_thuc !== null) {
            $dates = [];
            $current = $this->ngay_bat_dau->copy()->startOfDay();
            $end = $this->ngay_ket_thuc->copy()->startOfDay();

            while ($current->lte($end)) {
                $dates[] = $current->copy();
                $current->addDay();
            }

            return $dates;
        }

        return [$this->ngay_bat_dau->copy()->startOfDay()];
    }
}

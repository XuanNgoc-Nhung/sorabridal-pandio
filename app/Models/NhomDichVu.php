<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class NhomDichVu extends Model
{
    use HasFactory;

    protected $table = 'nhom_dich_vu';

    /** Trạng thái: ẩn */
    public const TRANG_THAI_AN = 0;

    /** Trạng thái: hiển thị */
    public const TRANG_THAI_HIEN_THI = 1;

    public const SAP_XEP_TEN = 'ten_nhom';

    public const SAP_XEP_GIA_TIEN = 'gia_tien';

    public const SAP_XEP_GIA_GOC = 'gia_goc';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_TEN;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_TEN => 'Tên nhóm',
        self::SAP_XEP_GIA_TIEN => 'Giá tiền',
        self::SAP_XEP_GIA_GOC => 'Giá gốc',
    ];

    /** @var array<int, string> */
    public const TRANG_THAI_LABELS = [
        self::TRANG_THAI_HIEN_THI => 'Hiển thị',
        self::TRANG_THAI_AN => 'Ẩn',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ten_nhom',
        'ma_nhom',
        'loai',
        'slug',
        'gia_tien',
        'gia_goc',
        'the',
        'ghi_chu',
        'mo_ta',
        'trang_thai',
        'nguoi_tao_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gia_tien' => 'decimal:2',
            'gia_goc' => 'decimal:2',
            'trang_thai' => 'integer',
        ];
    }

    /**
     * Boot: tự tạo slug từ tên nhóm nếu chưa có.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (NhomDichVu $model) {
            if (empty($model->slug) && ! empty($model->ten_nhom)) {
                $model->slug = Str::slug($model->ten_nhom);
            }
        });

        static::updating(function (NhomDichVu $model) {
            if ($model->isDirty('ten_nhom') && empty($model->slug)) {
                $model->slug = Str::slug($model->ten_nhom);
            }
        });
    }

    /**
     * Danh sách dịch vụ lẻ thuộc nhóm (many-to-many).
     */
    public function dichVuLe(): BelongsToMany
    {
        return $this->belongsToMany(
            DichVuLe::class,
            'nhom_dich_vu_dich_vu_le',
            'nhom_dich_vu_id',
            'dich_vu_le_id'
        )
            ->using(DichVuLeNhomDichVu::class)
            ->withPivot('so_luong')
            ->withTimestamps();
    }

    /**
     * Người tạo (user).
     */
    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }
}

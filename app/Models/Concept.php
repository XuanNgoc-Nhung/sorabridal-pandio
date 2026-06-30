<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concept extends Model
{
    use HasFactory;

    protected $table = 'concept';

    /** Trạng thái: đang hoạt động */
    public const TRANG_THAI_ACTIVE = 1;

    /** Trạng thái: ngưng hoạt động */
    public const TRANG_THAI_INACTIVE = 0;

    public const SAP_XEP_ID = 'id';

    public const SAP_XEP_TEN = 'ten_concept';

    public const SAP_XEP_DA_SU_DUNG = 'da_su_dung';

    public const SAP_XEP_TRANG_THAI = 'trang_thai';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_ID;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_DA_SU_DUNG => 'Đã sử dụng',
    ];

    /** @var array<int, string> */
    public const TRANG_THAI_LABELS = [
        self::TRANG_THAI_ACTIVE => 'Đang hoạt động',
        self::TRANG_THAI_INACTIVE => 'Ngưng hoạt động',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ten_concept',
        'ma_concept',
        'dia_diem',
        'hinh_anh',
        'trang_thai',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trang_thai' => 'integer',
        ];
    }

    public function hopDongCuoi(): HasMany
    {
        return $this->hasMany(HopDongCuoi::class, 'concept_id');
    }
}


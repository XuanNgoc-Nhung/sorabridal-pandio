<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class DichVuLe extends Model
{
    use HasFactory;

    protected $table = 'dich_vu_le';

    /** Trạng thái: ẩn */
    public const TRANG_THAI_AN = 0;

    /** Trạng thái: hiển thị */
    public const TRANG_THAI_HIEN_THI = 1;

    /** Tiêu chí sắp xếp: tên dịch vụ */
    public const SAP_XEP_TEN = 'ten_dich_vu';

    /** Tiêu chí sắp xếp: giá dịch vụ */
    public const SAP_XEP_GIA = 'gia_dich_vu';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ten_dich_vu',
        'ma_dich_vu',
        'loai',
        'slug',
        'mo_ta',
        'trang_thai',
        'ghi_chu',
        'gia_dich_vu',
        'nguoi_tao_id',
        'phong_ban_id',
        'don_vi',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gia_dich_vu' => 'decimal:2',
            'trang_thai' => 'integer',
        ];
    }

    /**
     * Boot: tự tạo slug từ tên dịch vụ nếu chưa có.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (DichVuLe $model) {
            if (empty($model->slug) && ! empty($model->ten_dich_vu)) {
                $model->slug = Str::slug($model->ten_dich_vu);
            }
        });

        static::updating(function (DichVuLe $model) {
            if ($model->isDirty('ten_dich_vu') && empty($model->slug)) {
                $model->slug = Str::slug($model->ten_dich_vu);
            }
        });
    }

    /**
     * Người tạo (user).
     */
    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    /**
     * Phòng ban phụ trách dịch vụ.
     */
    public function phongBan(): BelongsTo
    {
        return $this->belongsTo(PhongBan::class, 'phong_ban_id');
    }

    /**
     * Lọc dịch vụ có chứa phòng ban (theo id hoặc mã phòng ban cũ).
     */
    public function scopeCoPhongBan($query, int $phongBanId, ?string $maPhongBan = null)
    {
        return $query->where(function ($qb) use ($phongBanId, $maPhongBan) {
            $id = (string) $phongBanId;
            $qb->where('phong_ban_id', $id)
                ->orWhere('phong_ban_id', 'like', $id . ',%')
                ->orWhere('phong_ban_id', 'like', '%,' . $id . ',%')
                ->orWhere('phong_ban_id', 'like', '%,' . $id);

            if ($maPhongBan !== null && $maPhongBan !== '') {
                foreach ([$maPhongBan, strtoupper($maPhongBan), strtolower($maPhongBan)] as $ma) {
                    $qb->orWhere('phong_ban_id', $ma)
                        ->orWhere('phong_ban_id', 'like', $ma . ',%')
                        ->orWhere('phong_ban_id', 'like', '%,' . $ma . ',%')
                        ->orWhere('phong_ban_id', 'like', '%,' . $ma);
                }
            }
        });
    }

    /**
     * Các token phòng ban từ cột `phong_ban_id` (id số hoặc mã, ví dụ "1,2" hoặc "LTS01, KTS01").
     *
     * @return list<string>
     */
    public function phongBanTokens(): array
    {
        if ($this->phong_ban_id === null || $this->phong_ban_id === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            'trim',
            preg_split('/\s*,\s*/', (string) $this->phong_ban_id, -1, PREG_SPLIT_NO_EMPTY)
        )));
    }

    /**
     * Danh sách id phòng ban (hỗ trợ dữ liệu cũ lưu theo mã `ma_phong_ban`).
     *
     * @return list<int>
     */
    public function phongBanIdList(\Illuminate\Support\Collection $phongBans): array
    {
        $byId = $phongBans->keyBy('id');
        $byMa = $phongBans->keyBy(fn (PhongBan $pb) => strtoupper((string) $pb->ma_phong_ban));

        $ids = [];
        foreach ($this->phongBanTokens() as $token) {
            if (ctype_digit($token)) {
                $id = (int) $token;
                if ($byId->has($id)) {
                    $ids[] = $id;
                }
                continue;
            }

            $pb = $byMa->get(strtoupper($token));
            if ($pb) {
                $ids[] = (int) $pb->id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Tên phòng ban hiển thị (hỗ trợ id số và mã phòng ban).
     */
    public function tenPhongBanHienThi(\Illuminate\Support\Collection $phongBans): string
    {
        $byId = $phongBans->keyBy('id');
        $byMa = $phongBans->keyBy(fn (PhongBan $pb) => strtoupper((string) $pb->ma_phong_ban));

        $names = collect($this->phongBanTokens())
            ->map(function (string $token) use ($byId, $byMa) {
                if (ctype_digit($token)) {
                    return $byId->get((int) $token)?->ten_phong_ban;
                }

                return $byMa->get(strtoupper($token))?->ten_phong_ban;
            })
            ->filter()
            ->values();

        return $names->isNotEmpty() ? $names->implode(', ') : '—';
    }

    /**
     * Các hợp đồng có chọn dịch vụ lẻ này (many-to-many).
     */
    public function hopDong(): BelongsToMany
    {
        return $this->belongsToMany(
            HopDong::class,
            'hop_dong_dich_vu_le',
            'dich_vu_le_id',
            'hop_dong_id'
        )
            ->using(HopDongDichVuLe::class)
            ->withPivot('gia_goc', 'gia_thuc')
            ->withTimestamps();
    }

    /**
     * Các nhóm dịch vụ chứa dịch vụ lẻ này (many-to-many).
     */
    public function nhomDichVu(): BelongsToMany
    {
        return $this->belongsToMany(
            NhomDichVu::class,
            'nhom_dich_vu_dich_vu_le',
            'dich_vu_le_id',
            'nhom_dich_vu_id'
        )
            ->using(DichVuLeNhomDichVu::class)
            ->withPivot('so_luong')
            ->withTimestamps();
    }
}

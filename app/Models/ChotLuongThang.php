<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ChotLuongThang extends Model
{
    protected $table = 'chot_luong_thang';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'thang',
        'nam',
        'nguoi_chot_id',
        'ngay_chot',
        'du_lieu',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'thang' => 'integer',
            'nam' => 'integer',
            'ngay_chot' => 'datetime',
            'du_lieu' => 'array',
        ];
    }

    public function nguoiChot(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_chot_id');
    }

    public static function timTheoKy(int $thang, int $nam): ?self
    {
        return self::query()
            ->where('thang', $thang)
            ->where('nam', $nam)
            ->first();
    }

    public static function ngayChotBatDau(): int
    {
        return min(31, max(1, (int) config('tinh_luong.chot_luong.ngay_bat_dau', 1)));
    }

    public static function ngayChotKetThuc(): int
    {
        $ketThuc = min(31, max(1, (int) config('tinh_luong.chot_luong.ngay_ket_thuc', 10)));

        return max(self::ngayChotBatDau(), $ketThuc);
    }

    public static function khungChotLuongLabel(): string
    {
        return self::ngayChotBatDau().'–'.self::ngayChotKetThuc();
    }

    /**
     * @return array{thang: int, nam: int}
     */
    public static function thangNamTruoc(?Carbon $now = null): array
    {
        $truoc = ($now ?? now())->copy()->subMonth();

        return [
            'thang' => (int) $truoc->month,
            'nam' => (int) $truoc->year,
        ];
    }

    public static function trongKhungChotLuong(?Carbon $now = null): bool
    {
        $ngay = (int) ($now ?? now())->day;

        return $ngay >= self::ngayChotBatDau() && $ngay <= self::ngayChotKetThuc();
    }

    public static function laThangTruoc(int $thang, int $nam, ?Carbon $now = null): bool
    {
        $ky = self::thangNamTruoc($now);

        return $thang === $ky['thang'] && $nam === $ky['nam'];
    }

    public static function lyDoKhongDuocChot(int $thang, int $nam, ?Carbon $now = null): ?string
    {
        $now = $now ?? now();

        if (! self::trongKhungChotLuong($now)) {
            return 'Chỉ được chốt lương từ ngày '
                .self::khungChotLuongLabel().' hàng tháng.';
        }

        if (! self::laThangTruoc($thang, $nam, $now)) {
            $ky = self::thangNamTruoc($now);

            return 'Chỉ được chốt lương cho tháng trước (tháng '.$ky['thang'].'/'.$ky['nam'].').';
        }

        return null;
    }

    public static function coTheChotThang(int $thang, int $nam, ?Carbon $now = null): bool
    {
        return self::lyDoKhongDuocChot($thang, $nam, $now) === null;
    }

    /**
     * @return list<int>
     */
    public function daChuyenUserIds(): array
    {
        return array_map('intval', $this->du_lieu['da_chuyen_user_ids'] ?? []);
    }

    public function daChuyenChoUser(int $userId): bool
    {
        return in_array($userId, $this->daChuyenUserIds(), true);
    }

    public function danhDauDaChuyen(int $userId): void
    {
        $duLieu = $this->du_lieu ?? [];
        $ids = array_map('intval', $duLieu['da_chuyen_user_ids'] ?? []);

        if (in_array($userId, $ids, true)) {
            return;
        }

        $ids[] = $userId;
        $duLieu['da_chuyen_user_ids'] = array_values($ids);
        $this->update(['du_lieu' => $duLieu]);
    }
}

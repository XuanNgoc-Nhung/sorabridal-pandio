<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiemDanh extends Model
{
    use HasFactory;

    protected $table = 'diem_danh';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'gio_vao',
        'gio_ra',
        'di_muon',
        'thoi_gian_di_muon',
        'thoi_gian_ve_som',
        'tien_phat_di_muon',
        'tien_phat_ve_som',
        'ip_checkin',
        'ip_checkout',
        'hop_le',
        'ly_do',
        'nghi_phep',
        'loai_phep',
        'ghi_chu',
        'gio_lam_co_ban',
        'gio_lam_tang_ca',
        'luong_co_ban',
        'luong_tang_ca',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gio_vao' => 'datetime',
            'gio_ra' => 'datetime',
            'di_muon' => 'boolean',
            'thoi_gian_di_muon' => 'integer',
            'thoi_gian_ve_som' => 'integer',
            'tien_phat_di_muon' => 'integer',
            'tien_phat_ve_som' => 'integer',
            'hop_le' => 'boolean',
            'nghi_phep' => 'boolean',
            'gio_lam_co_ban' => 'decimal:2',
            'gio_lam_tang_ca' => 'decimal:2',
            'luong_co_ban' => 'decimal:2',
            'luong_tang_ca' => 'decimal:2',
        ];
    }

    /**
     * Liên kết với bảng users (người điểm danh).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chamCong(): HasMany
    {
        return $this->hasMany(ChamCong::class, 'diem_danh_id', 'id');
    }

    /**
     * Khi duyệt đơn xin đi muộn / về sớm: bỏ tiền phạt tương ứng trên bản ghi điểm danh cùng ngày.
     */
    public static function capNhatTienPhatTuDonNghiPhepDaDuyet(XinNghiPhep $don): void
    {
        if (! in_array($don->loai_nghi_phep, [XinNghiPhep::LOAI_DI_MUON, XinNghiPhep::LOAI_VE_SOM], true)) {
            return;
        }

        foreach ($don->cacNgayApDung() as $ngay) {
            $diemDanh = self::query()
                ->where('user_id', $don->user_id)
                ->whereDate('gio_vao', $ngay)
                ->first();

            if ($diemDanh === null) {
                continue;
            }

            $capNhat = match ($don->loai_nghi_phep) {
                XinNghiPhep::LOAI_DI_MUON => self::coPhatDiMuon($diemDanh)
                    ? ['tien_phat_di_muon' => 0]
                    : [],
                XinNghiPhep::LOAI_VE_SOM => self::coPhatVeSom($diemDanh)
                    ? ['tien_phat_ve_som' => 0]
                    : [],
                default => [],
            };

            if ($capNhat !== []) {
                $diemDanh->update($capNhat);
            }
        }
    }

    private static function coPhatDiMuon(self $diemDanh): bool
    {
        return ($diemDanh->thoi_gian_di_muon ?? 0) > 0
            || ($diemDanh->tien_phat_di_muon ?? 0) > 0
            || $diemDanh->di_muon;
    }

    private static function coPhatVeSom(self $diemDanh): bool
    {
        return ($diemDanh->thoi_gian_ve_som ?? 0) > 0
            || ($diemDanh->tien_phat_ve_som ?? 0) > 0;
    }
}

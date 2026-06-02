<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaoCaoAds extends Model
{
    use HasFactory;

    public const SAP_XEP_ID = 'id';

    public const SAP_XEP_NGAY = 'ngay';

    public const SAP_XEP_ADS_TIKTOK = 'ads_tiktok';

    public const SAP_XEP_ADS_FB = 'ads_fb';

    public const SAP_XEP_KHACH_MOI = 'khach_moi';

    public const SAP_XEP_LICH_HEN = 'lich_hen';

    public const SAP_XEP_CPL = 'cpl';

    public const SAP_XEP_ROAS = 'roas';

    public const SAP_XEP_TY_LE_HEN_TREN_KHACH = 'ty_le_hen_tren_khach';

    public const SAP_XEP_KHACH_DEN_CUA_HANG = 'khach_den_cua_hang';

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_ID;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_ID => 'Mới nhất',
        self::SAP_XEP_NGAY => 'Ngày',
        self::SAP_XEP_ADS_TIKTOK => 'Ads TikTok',
        self::SAP_XEP_ADS_FB => 'Ads Facebook',
        self::SAP_XEP_KHACH_MOI => 'Khách mới',
        self::SAP_XEP_LICH_HEN => 'Lịch hẹn',
        self::SAP_XEP_CPL => 'CPL',
        self::SAP_XEP_ROAS => 'ROAS',
        self::SAP_XEP_TY_LE_HEN_TREN_KHACH => 'Tỷ lệ hẹn / khách',
        self::SAP_XEP_KHACH_DEN_CUA_HANG => 'Khách đến cửa hàng',
        self::SAP_XEP_CREATED_AT => 'Ngày tạo',
    ];

    protected $table = 'bao_cao_ads';

    protected $fillable = [
        'ngay',
        'ads_tiktok',
        'ads_fb',
        'khach_moi',
        'lich_hen',
        'cpl',
        'roas',
        'ty_le_hen_tren_khach',
        'khach_den_cua_hang',
    ];
}

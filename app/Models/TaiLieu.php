<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaiLieu extends Model
{
    use HasFactory;

    public const SAP_XEP_TEN = 'ten_tai_lieu';

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_CREATED_AT;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_TEN => 'Tên tài liệu',
        self::SAP_XEP_CREATED_AT => 'Thời gian tạo',
    ];

    protected $table = 'tai_lieu';

    protected $fillable = [
        'ten_tai_lieu',
        'mo_ta',
        'file',
        'duong_dan',
    ];
}

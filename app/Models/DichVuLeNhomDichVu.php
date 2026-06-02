<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DichVuLeNhomDichVu extends Pivot
{
    protected $table = 'nhom_dich_vu_dich_vu_le';

    /** Khóa ngoại phía NhomDichVu */
    protected $foreignKey = 'nhom_dich_vu_id';

    /** Khóa ngoại phía DichVuLe */
    protected $relatedKey = 'dich_vu_le_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nhom_dich_vu_id',
        'dich_vu_le_id',
        'so_luong',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'so_luong' => 'integer',
        ];
    }
}

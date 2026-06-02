<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    use HasFactory;

    protected $table = 'vai_tro';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ma_vai_tro',
        'ten_vai_tro',
        'mo_ta',
        'ghi_chu',
        'ds_menu',
    ];

    protected function casts(): array
    {
        return [
            'ds_menu' => 'array',
        ];
    }
}

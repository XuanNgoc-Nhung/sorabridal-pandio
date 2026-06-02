<?php

namespace App\Support;

use Illuminate\Http\Request;

class AdminPagination
{
    /** @var list<int> */
    public const OPTIONS = [10, 25, 50, 100];

    public const DEFAULT = 25;

    public static function perPage(?Request $request = null): int
    {
        $request ??= request();
        $perPage = (int) $request->query('per_page', self::DEFAULT);

        return in_array($perPage, self::OPTIONS, true) ? $perPage : self::DEFAULT;
    }
}

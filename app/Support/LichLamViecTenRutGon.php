<?php

namespace App\Support;

/**
 * Rút gọn họ tên cặp đôi trên lịch làm việc & danh sách HĐ.
 * Tên thuộc ten_rut_gon_dac_biet (vd. Anh) → tên đệm + tên; ngược lại chỉ tên.
 */
class LichLamViecTenRutGon
{
    /** @return list<string> */
    public static function tenDacBiet(): array
    {
        $ten = config('lich_lam_viec.ten_rut_gon_dac_biet', ['Anh']);

        return array_values(array_filter(array_map(
            fn ($item) => trim((string) $item),
            is_array($ten) ? $ten : []
        )));
    }

    public static function hoTen(?string $hoTen): string
    {
        $hoTen = trim((string) ($hoTen ?? ''));
        if ($hoTen === '') {
            return '';
        }

        $parts = preg_split('/\s+/u', $hoTen, -1, PREG_SPLIT_NO_EMPTY);
        if ($parts === false || $parts === []) {
            return $hoTen;
        }

        if (count($parts) === 1) {
            return $parts[0];
        }

        $ten = $parts[count($parts) - 1];
        $tenLower = mb_strtolower($ten);
        $dacBiet = array_map(
            fn (string $item) => mb_strtolower($item),
            self::tenDacBiet()
        );

        if (in_array($tenLower, $dacBiet, true)) {
            return $parts[count($parts) - 2].' '.$ten;
        }

        return $ten;
    }

    public static function hoTenHienThi(?string $hoTen): string
    {
        $rutGon = self::hoTen($hoTen);

        return $rutGon !== '' ? $rutGon : '—';
    }
}

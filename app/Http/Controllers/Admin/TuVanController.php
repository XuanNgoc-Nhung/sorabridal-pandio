<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKyTuVan;
use App\Support\AdminPagination;
use Illuminate\Http\Request;

class TuVanController extends Controller
{
    public function danhSach(Request $request)
    {
        $validated = $request->validate([
            'tu_khoa' => 'nullable|string|max:200',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(DangKyTuVan::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = DangKyTuVan::query();

        $tuKhoa = trim((string) ($validated['tu_khoa'] ?? ''));
        if ($tuKhoa !== '') {
            $like = '%'.addcslashes($tuKhoa, '%_\\').'%';

            $query->where(function ($q) use ($like) {
                $q->where('ten_co_dau', 'like', $like)
                    ->orWhere('ten_chu_re', 'like', $like)
                    ->orWhere('so_dien_thoai', 'like', $like)
                    ->orWhere('phim_truong_quan_tam', 'like', $like)
                    ->orWhere('goi_dich_vu_quan_tam', 'like', $like)
                    ->orWhere('ghi_chu', 'like', $like);
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? DangKyTuVan::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, DangKyTuVan::SAP_XEP_OPTIONS)) {
            $sapXepTheo = DangKyTuVan::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            DangKyTuVan::SAP_XEP_NGAY_CUOI => $query
                ->orderByRaw('ngay_cuoi_du_kien IS NULL')
                ->orderBy('ngay_cuoi_du_kien', $thuTu),
            default => $query->orderBy('created_at', $thuTu),
        };
        $query->orderBy('id', $thuTu);

        $dangKyTuVans = $query->paginate(AdminPagination::perPage())->withQueryString();

        return view('admin.tu-van.danh-sach', compact('dangKyTuVans'));
    }
}

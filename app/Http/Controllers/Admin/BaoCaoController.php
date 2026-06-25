<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaoCaoAds;
use App\Support\AdminPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BaoCaoController extends Controller
{
    public function baoCaoAds(Request $request)
    {
        $validated = $request->validate([
            'tu_khoa' => 'nullable|string|max:200',
            'tu_ngay' => 'nullable|date',
            'den_ngay' => [
                'nullable',
                'date',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    $tu = $request->input('tu_ngay');
                    if ($tu && $value && strtotime((string) $value) < strtotime((string) $tu)) {
                        $fail('Đến ngày phải sau hoặc bằng từ ngày.');
                    }
                },
            ],
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(BaoCaoAds::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = BaoCaoAds::query();

        $tuKhoa = trim((string) ($validated['tu_khoa'] ?? ''));
        if ($tuKhoa !== '') {
            $likeTuKhoa = '%'.addcslashes($tuKhoa, '%_\\').'%';
            $query->where(function ($qb) use ($likeTuKhoa) {
                foreach ([
                    'ngay',
                    'ads_tiktok',
                    'ads_fb',
                    'cpqc_google',
                    'khach_moi',
                    'lich_hen',
                    'cpl',
                    'roas',
                    'ty_le_hen_tren_khach',
                    'khach_den_cua_hang',
                ] as $cot) {
                    $qb->orWhere($cot, 'like', $likeTuKhoa);
                }
            });
        }

        $tuNgay = $validated['tu_ngay'] ?? null;
        $denNgay = $validated['den_ngay'] ?? null;
        if ($tuNgay || $denNgay) {
            $from = $tuNgay ? Carbon::parse($tuNgay)->format('Y-m-d') : null;
            $to = $denNgay ? Carbon::parse($denNgay)->format('Y-m-d') : null;
            if ($from && $to && $from > $to) {
                [$from, $to] = [$to, $from];
            }
            $ngayExpr = "STR_TO_DATE(ngay, '%d/%m/%Y')";
            $query->whereNotNull('ngay')->where('ngay', '!=', '');
            if ($from && $to) {
                $query->whereRaw("{$ngayExpr} BETWEEN ? AND ?", [$from, $to]);
            } elseif ($from) {
                $query->whereRaw("{$ngayExpr} >= ?", [$from]);
            } elseif ($to) {
                $query->whereRaw("{$ngayExpr} <= ?", [$to]);
            }
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? BaoCaoAds::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, BaoCaoAds::SAP_XEP_OPTIONS)) {
            $sapXepTheo = BaoCaoAds::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            BaoCaoAds::SAP_XEP_NGAY => $query->orderBy('ngay', $thuTu),
            BaoCaoAds::SAP_XEP_ADS_TIKTOK => $query->orderBy('ads_tiktok', $thuTu),
            BaoCaoAds::SAP_XEP_ADS_FB => $query->orderBy('ads_fb', $thuTu),
            BaoCaoAds::SAP_XEP_CPQC_GOOGLE => $query->orderBy('cpqc_google', $thuTu),
            BaoCaoAds::SAP_XEP_KHACH_MOI => $query->orderBy('khach_moi', $thuTu),
            BaoCaoAds::SAP_XEP_LICH_HEN => $query->orderBy('lich_hen', $thuTu),
            BaoCaoAds::SAP_XEP_CPL => $query->orderBy('cpl', $thuTu),
            BaoCaoAds::SAP_XEP_ROAS => $query->orderBy('roas', $thuTu),
            BaoCaoAds::SAP_XEP_TY_LE_HEN_TREN_KHACH => $query->orderBy('ty_le_hen_tren_khach', $thuTu),
            BaoCaoAds::SAP_XEP_KHACH_DEN_CUA_HANG => $query->orderBy('khach_den_cua_hang', $thuTu),
            BaoCaoAds::SAP_XEP_CREATED_AT => $query->orderBy('created_at', $thuTu),
            default => $query->orderBy('id', $thuTu),
        };
        if ($sapXepTheo !== BaoCaoAds::SAP_XEP_ID) {
            $query->orderBy('id', $thuTu);
        }

        $danhSach = $query
            ->paginate(AdminPagination::perPage())
            ->withQueryString();

        return view('admin.bao-cao.bao-cao-ads', compact('danhSach'));
    }

    public function storeBaoCaoAds(Request $request)
    {
        $data = $this->validatedBaoCaoAds($request);

        BaoCaoAds::create($data);

        return redirect()
            ->route('admin.bao-cao.ads')
            ->with('success', 'Đã thêm báo cáo Ads thành công.');
    }

    public function updateBaoCaoAds(Request $request, BaoCaoAds $baoCaoAd)
    {
        $data = $this->validatedBaoCaoAds($request);

        $baoCaoAd->update($data);

        return redirect()
            ->route('admin.bao-cao.ads')
            ->with('success', 'Đã cập nhật báo cáo Ads thành công.');
    }

    public function destroyBaoCaoAds(BaoCaoAds $baoCaoAd)
    {
        $baoCaoAd->delete();

        return redirect()
            ->route('admin.bao-cao.ads')
            ->with('success', 'Đã xoá báo cáo Ads.');
    }

    /**
     * @return array<string, string|null>
     */
    private function validatedBaoCaoAds(Request $request): array
    {
        $validated = $request->validate([
            'ngay' => 'nullable|string|max:255',
            'ads_tiktok' => 'nullable|string|max:255',
            'ads_fb' => 'nullable|string|max:255',
            'cpqc_google' => 'nullable|string|max:255',
            'khach_moi' => 'nullable|string|max:255',
            'lich_hen' => 'nullable|string|max:255',
            'cpl' => 'nullable|string|max:255',
            'roas' => 'nullable|string|max:255',
            'ty_le_hen_tren_khach' => 'nullable|string|max:255',
            'khach_den_cua_hang' => 'nullable|string|max:255',
        ]);

        return array_map(
            fn ($value) => $value === null || $value === '' ? null : (string) $value,
            $validated
        );
    }
}

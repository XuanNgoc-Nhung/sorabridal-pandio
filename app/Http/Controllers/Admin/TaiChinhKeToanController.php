<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChamCong;
use App\Models\HopDongChoThueTrangPhuc;
use App\Models\HopDongCuoi;
use App\Models\PhieuThuChi;
use App\Models\User;
use App\Support\AdminCongNoList;
use App\Support\AdminPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class TaiChinhKeToanController extends Controller
{
    public function index()
    {
        return view('admin.tai-chinh.index');
    }

    public function congNo(Request $request)
    {
        $validated = $request->validate([
            'tu_ngay' => 'nullable|date',
            'den_ngay' => 'nullable|date',
            'trang_thai_tt' => 'nullable|in:chua,da',
            'search' => 'nullable|string|max:200',
            'loai_hop_dong' => 'nullable|string|in:'.implode(',', array_keys(AdminCongNoList::LOAI_HOP_DONG_OPTIONS)),
            'khoang_ngay' => 'nullable|string|in:'.implode(',', array_keys(AdminCongNoList::KHOANG_NGAY_OPTIONS)),
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(AdminCongNoList::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $khoangNgay = trim((string) ($validated['khoang_ngay'] ?? ''));
        if ($khoangNgay !== '') {
            $range = AdminCongNoList::resolveKhoangNgay($khoangNgay);
            if ($range !== null) {
                $request->merge([
                    'tu_ngay' => $range['tu'],
                    'den_ngay' => $range['den'],
                ]);
            }
        }

        $search = trim((string) ($validated['search'] ?? ''));
        $loaiHopDong = $validated['loai_hop_dong'] ?? '';
        $likeSearch = $search !== '' ? '%'.addcslashes($search, '%_\\').'%' : null;

        $includeCuoi = $loaiHopDong === '' || $loaiHopDong === AdminCongNoList::LOAI_HOP_DONG_CUOI;
        $includeThue = $loaiHopDong === '' || $loaiHopDong === AdminCongNoList::LOAI_HOP_DONG_THUE;

        $danhSachTongHop = collect();

        if ($includeCuoi) {
            $hopDongCuoiQuery = HopDongCuoi::query()->where('tong_tien', '>', 0);
            $this->applyCongNoDateRange($hopDongCuoiQuery, $request);
            $this->applyCongNoKeywordCuoi($hopDongCuoiQuery, $likeSearch, $search);

            $danhSachTongHop = $danhSachTongHop->concat(
                $hopDongCuoiQuery->get()->map(fn (HopDongCuoi $hopDong): object => $this->mapCongNoHopDongCuoi($hopDong))
            );
        }

        if ($includeThue) {
            $hopDongTrangPhucQuery = HopDongChoThueTrangPhuc::query()->where('tong_tien', '>', 0);
            $this->applyCongNoDateRange($hopDongTrangPhucQuery, $request);
            $this->applyCongNoKeywordThue($hopDongTrangPhucQuery, $likeSearch, $search);

            $danhSachTongHop = $danhSachTongHop->concat(
                $hopDongTrangPhucQuery->get()->map(fn (HopDongChoThueTrangPhuc $hopDong): object => $this->mapCongNoHopDongThue($hopDong))
            );
        }

        $danhSachTongHop = $danhSachTongHop
            ->filter(function (object $item) use ($request): bool {
                $trangThaiTt = $request->input('trang_thai_tt');

                if ($trangThaiTt === 'chua') {
                    return (float) $item->con_lai > 0.00001;
                }
                if ($trangThaiTt === 'da') {
                    return (float) $item->con_lai <= 0.00001;
                }

                return true;
            })
            ->pipe(fn ($items) => $this->sortCongNoCollection($items, $validated))
            ->values();

        $tongHop = [
            'so_hop_dong' => $danhSachTongHop->count(),
            'tong_tien' => $danhSachTongHop->sum('tong_tien'),
            'da_thanh_toan' => $danhSachTongHop->sum('da_thanh_toan'),
            'con_lai' => $danhSachTongHop->sum('con_lai'),
        ];

        $perPage = AdminPagination::perPage($request);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $danhSach = new LengthAwarePaginator(
            $danhSachTongHop->forPage($currentPage, $perPage)->values(),
            $danhSachTongHop->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('admin.tai-chinh.cong-no', compact('danhSach', 'tongHop'));
    }

    public function phieuThuChi(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:200',
            'loai_phieu' => 'nullable|in:1,2',
            'trang_thai' => 'nullable|in:-1,0,1,2',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(PhieuThuChi::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = PhieuThuChi::query()->with(['nguoiTao', 'nguoiDuyet']);

        $search = trim((string) ($validated['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($qb) use ($like) {
                $qb->where('ly_do', 'like', $like)
                    ->orWhere('ghi_chu', 'like', $like);
            });
        }

        if ($request->filled('loai_phieu')) {
            $query->where('loai_phieu', (int) $request->loai_phieu);
        }
        if ($request->filled('trang_thai') && $request->trang_thai !== '') {
            $query->where('trang_thai', (int) $request->trang_thai);
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? PhieuThuChi::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, PhieuThuChi::SAP_XEP_OPTIONS)) {
            $sapXepTheo = PhieuThuChi::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            PhieuThuChi::SAP_XEP_SO_TIEN => $query->orderBy('so_tien', $thuTu),
            PhieuThuChi::SAP_XEP_NGAY_DUYET => $query->orderBy('ngay_duyet', $thuTu),
            default => $query->orderBy('ngay_duyet', $thuTu),
        };
        $query->orderByDesc('id');

        $danhSach = $query->paginate(AdminPagination::perPage())->withQueryString();

        return view('admin.tai-chinh.phieu-thu-chi', compact('danhSach'));
    }

    public function storeCongNo(Request $request)
    {
        $validated = $request->validate([
            'ten_cong_no' => 'required|string|max:255',
            'so_tien' => 'required|numeric|min:0',
        ]);
    }

    public function updateCongNo(Request $request, CongNo $congNo)
    {
        $validated = $request->validate([
            'ten_cong_no' => 'required|string|max:255',
            'so_tien' => 'required|numeric|min:0',
        ]);
    }

    public function destroyCongNo(CongNo $congNo)
    {
        $congNo->delete();

        return redirect()->route('admin.tai-chinh.cong-no')->with('success', 'Đã xóa công nợ thành công.');
    }

    public function storePhieuThuChi(Request $request)
    {
        $validated = $request->validate([
            'loai_phieu' => 'required|in:1,2',
            'so_tien' => 'required|numeric|min:0',
            'ly_do' => 'required|string|max:255',
            'ghi_chu' => 'nullable|string|max:500',
        ]);
        $validated['nguoi_tao_id'] = $request->user()->id;
        $validated['trang_thai'] = PhieuThuChi::TRANG_THAI_CHO_XU_LY;
        PhieuThuChi::create($validated);

        return redirect()->route('admin.tai-chinh.phieu-thu-chi')->with('success', 'Đã thêm phiếu thu chi thành công.');
    }

    public function updatePhieuThuChi(Request $request, PhieuThuChi $phieuThuChi)
    {
        $validated = $request->validate([
            'loai_phieu' => 'required|in:1,2',
            'so_tien' => 'required|numeric|min:0',
            'ly_do' => 'required|string|max:255',
            'trang_thai' => 'nullable|in:-1,0,1,2',
            'ghi_chu' => 'nullable|string|max:500',
        ]);
        $validated['trang_thai'] = (int) ($validated['trang_thai'] ?? $phieuThuChi->trang_thai);
        $phieuThuChi->update($validated);

        return redirect()->route('admin.tai-chinh.phieu-thu-chi')->with('success', 'Đã cập nhật phiếu thu chi thành công.');
    }

    public function destroyPhieuThuChi(PhieuThuChi $phieuThuChi)
    {
        $phieuThuChi->delete();

        return redirect()->route('admin.tai-chinh.phieu-thu-chi')->with('success', 'Đã xóa phiếu thu chi thành công.');
    }

    public function duyetPhieuThuChi(PhieuThuChi $phieuThuChi)
    {
        if ((int) $phieuThuChi->trang_thai !== PhieuThuChi::TRANG_THAI_CHO_XU_LY) {
            return redirect()->route('admin.tai-chinh.phieu-thu-chi')->with('error', 'Chỉ được duyệt phiếu đang chờ xử lý.');
        }
        $phieuThuChi->update([
            'trang_thai' => PhieuThuChi::TRANG_THAI_DONG_Y,
            'nguoi_duyet_id' => request()->user()->id,
            'ngay_duyet' => now(),
        ]);

        return redirect()->route('admin.tai-chinh.phieu-thu-chi')->with('success', 'Đã duyệt phiếu thu chi.');
    }

    public function huyPhieuThuChi(PhieuThuChi $phieuThuChi)
    {
        if ((int) $phieuThuChi->trang_thai !== PhieuThuChi::TRANG_THAI_CHO_XU_LY) {
            return redirect()->route('admin.tai-chinh.phieu-thu-chi')->with('error', 'Chỉ được hủy phiếu đang chờ xử lý.');
        }
        $phieuThuChi->update([
            'trang_thai' => PhieuThuChi::TRANG_THAI_TU_CHOI,
            'nguoi_duyet_id' => request()->user()->id,
            'ngay_duyet' => now(),
        ]);

        return redirect()->route('admin.tai-chinh.phieu-thu-chi')->with('success', 'Đã hủy phiếu thu chi.');
    }

    public function tinhLuong(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }
        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $startStr = $start->format('Y-m-d');
        $endStr = $end->format('Y-m-d');

        $ngayTrongThang = [];
        for ($d = (clone $start); $d->lte($end); $d->addDay()) {
            $ngayTrongThang[] = (clone $d);
        }

        // User có chấm công trong tháng (giống màn chấm công) để dữ liệu điểm danh khớp
        $userIdsCoChamCong = ChamCong::query()
            ->whereBetween('ngay_diem_danh', [$startStr, $endStr])
            ->distinct()
            ->pluck('user_id');

        $nhanVien = User::query()
            ->where(function ($q) use ($userIdsCoChamCong) {
                $q->where('role', User::ROLE_NHAN_VIEN)
                    ->orWhereIn('id', $userIdsCoChamCong);
            })
            ->orderBy('name')
            ->get();

        $chamCong = ChamCong::query()
            ->with(['user', 'diemDanh'])
            ->whereBetween('ngay_diem_danh', [$startStr, $endStr])
            ->whereIn('user_id', $nhanVien->pluck('id'))
            ->get();

        $bangChamCong = [];
        $bangLuongThang = [];
        foreach ($nhanVien as $u) {
            $bangLuongThang[$u->id] = [
                'luong_co_ban' => 0,
                'luong_tang_ca' => 0,
                'tong_luong' => 0,
            ];
        }
        foreach ($chamCong as $record) {
            $date = $record->ngay_diem_danh;
            $dateKey = $date ? Carbon::parse($date)->format('Y-m-d') : null;
            if (! $dateKey) {
                continue;
            }
            $bangChamCong[$dateKey][$record->user_id] = $record;

            $diemDanh = $record->diemDanh;
            if ($diemDanh) {
                $uid = $record->user_id;
                $bangLuongThang[$uid]['luong_co_ban'] += (float) ($diemDanh->luong_co_ban ?? 0);
                $bangLuongThang[$uid]['luong_tang_ca'] += (float) ($diemDanh->luong_tang_ca ?? 0);
            }
        }
        foreach (array_keys($bangLuongThang) as $uid) {
            $bangLuongThang[$uid]['tong_luong'] = $bangLuongThang[$uid]['luong_co_ban'] + $bangLuongThang[$uid]['luong_tang_ca'];
        }

        return view('admin.tai-chinh.tinh-luong', [
            'month' => $month,
            'year' => $year,
            'start' => $start,
            'end' => $end,
            'ngayTrongThang' => $ngayTrongThang,
            'nhanVien' => $nhanVien,
            'bangChamCong' => $bangChamCong,
            'bangLuongThang' => $bangLuongThang,
        ]);
    }

    public function storeTinhLuong(Request $request)
    {
        $validated = $request->validate([
            'thang' => 'required|integer|min:1|max:12',
        ]);
    }

    /**
     * @param  Builder<HopDongCuoi>|Builder<HopDongChoThueTrangPhuc>  $query
     */
    private function applyCongNoDateRange(Builder $query, Request $request): void
    {
        if ($request->filled('tu_ngay')) {
            $query->whereDate('created_at', '>=', $request->input('tu_ngay'));
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('created_at', '<=', $request->input('den_ngay'));
        }
    }

    /**
     * @param  Builder<HopDongCuoi>  $query
     */
    private function applyCongNoKeywordCuoi(Builder $query, ?string $like, string $keyword): void
    {
        if ($like === null) {
            return;
        }

        $query->where(function (Builder $qb) use ($like, $keyword): void {
            $qb->where('ten_chu_re', 'like', $like)
                ->orWhere('ten_co_dau', 'like', $like)
                ->orWhere('ma_hop_dong', 'like', $like)
                ->orWhereRaw("CONCAT('HDC-', id) LIKE ?", [$like]);
            if (ctype_digit($keyword)) {
                $qb->orWhere('id', (int) $keyword);
            }
        });
    }

    /**
     * @param  Builder<HopDongChoThueTrangPhuc>  $query
     */
    private function applyCongNoKeywordThue(Builder $query, ?string $like, string $keyword): void
    {
        if ($like === null) {
            return;
        }

        $query->where(function (Builder $qb) use ($like, $keyword): void {
            $qb->where('ten_khach_hang', 'like', $like)
                ->orWhereRaw("CONCAT('HDTP-', id) LIKE ?", [$like]);
            if (ctype_digit($keyword)) {
                $qb->orWhere('id', (int) $keyword);
            }
        });
    }

    private function mapCongNoHopDongCuoi(HopDongCuoi $hopDong): object
    {
        $tenKhachHang = collect([$hopDong->ten_chu_re, $hopDong->ten_co_dau])
            ->filter(fn ($ten) => filled($ten))
            ->implode(' - ');

        $tongTien = max(0, (float) ($hopDong->tong_tien ?? 0));
        $daThanhToan = max(0, (float) ($hopDong->tien_coc ?? 0));

        return (object) [
            'nguon_du_lieu' => AdminCongNoList::LOAI_HOP_DONG_CUOI,
            'loai_hop_dong' => AdminCongNoList::LOAI_HOP_DONG_OPTIONS[AdminCongNoList::LOAI_HOP_DONG_CUOI],
            'id' => $hopDong->id,
            'ma_hop_dong' => $hopDong->ma_hop_dong ?: 'HDC-'.$hopDong->id,
            'ten_khach_hang' => $tenKhachHang !== '' ? $tenKhachHang : '—',
            'created_at' => $hopDong->created_at,
            'ngay_bat_dau_hop_dong' => $hopDong->ngay_ky_hop_dong ?? $hopDong->created_at,
            'ngay_ket_thuc_hop_dong' => $hopDong->ngay_cuoi_chinh_thuc ?? $hopDong->ngay_cuoi_du_kien,
            'tong_tien' => $tongTien,
            'da_thanh_toan' => $daThanhToan,
            'tien_coc' => $daThanhToan,
            'con_lai' => max(0, $tongTien - $daThanhToan),
        ];
    }

    private function mapCongNoHopDongThue(HopDongChoThueTrangPhuc $hopDong): object
    {
        $tongTien = max(0, (float) ($hopDong->tong_tien ?? 0));
        $daThanhToan = max(0, (float) ($hopDong->tien_coc ?? 0));

        return (object) [
            'nguon_du_lieu' => AdminCongNoList::LOAI_HOP_DONG_THUE,
            'loai_hop_dong' => AdminCongNoList::LOAI_HOP_DONG_OPTIONS[AdminCongNoList::LOAI_HOP_DONG_THUE],
            'id' => $hopDong->id,
            'ma_hop_dong' => 'HDTP-'.$hopDong->id,
            'ten_khach_hang' => $hopDong->ten_khach_hang ?: '—',
            'created_at' => $hopDong->created_at,
            'ngay_bat_dau_hop_dong' => $hopDong->ngay_thue ?? $hopDong->created_at,
            'ngay_ket_thuc_hop_dong' => $hopDong->ngay_tra_chinh_thuc ?? $hopDong->ngay_tra_du_kien,
            'tong_tien' => $tongTien,
            'da_thanh_toan' => $daThanhToan,
            'tien_coc' => $daThanhToan,
            'con_lai' => max(0, $tongTien - $daThanhToan),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $items
     * @param  array<string, mixed>  $validated
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function sortCongNoCollection(\Illuminate\Support\Collection $items, array $validated): \Illuminate\Support\Collection
    {
        $sapXepTheo = $validated['sap_xep_theo'] ?? AdminCongNoList::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, AdminCongNoList::SAP_XEP_OPTIONS)) {
            $sapXepTheo = AdminCongNoList::SAP_XEP_MAC_DINH;
        }

        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
        $descending = $thuTu === 'desc';

        $sortKey = match ($sapXepTheo) {
            AdminCongNoList::SAP_XEP_TEN_KHACH => 'ten_khach_hang',
            AdminCongNoList::SAP_XEP_TONG_TIEN => 'tong_tien',
            AdminCongNoList::SAP_XEP_DA_THANH_TOAN => 'da_thanh_toan',
            AdminCongNoList::SAP_XEP_CON_LAI => 'con_lai',
            default => null,
        };

        if ($sortKey !== null) {
            return $descending
                ? $items->sortByDesc($sortKey)->values()
                : $items->sortBy($sortKey)->values();
        }

        return $descending
            ? $items->sortByDesc(fn (object $item) => $item->created_at?->timestamp ?? 0)->values()
            : $items->sortBy(fn (object $item) => $item->created_at?->timestamp ?? 0)->values();
    }
}

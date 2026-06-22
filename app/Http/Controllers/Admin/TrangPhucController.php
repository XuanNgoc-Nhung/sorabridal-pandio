<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HopDongChoThueTrangPhuc;
use App\Models\HopDongCuoi;
use App\Models\HopDongCuoiTrangPhuc;
use App\Models\SanPhamChoThue;
use App\Models\TrangPhuc;
use App\Support\AdminPagination;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TrangPhucController extends Controller
{
    /** Mỗi dòng sản phẩm = một đơn vị vật lý duy nhất (không còn bảng kho). */
    private const SO_LUONG_TINH_TE_TOI_DA = 1;

    public function index()
    {
        return redirect()->route('admin.trang-phuc.san-pham');
    }

    public function sanPham()
    {
        return $this->danhSachSanPham(request());
    }

    public function danhSachSanPham(Request $request)
    {
        $validated = $request->validate([
            'tu_khoa' => 'nullable|string|max:200',
            'search' => 'nullable|string|max:200',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(TrangPhuc::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
            'loc_hinh_anh' => ['nullable', 'string', Rule::in(array_keys(TrangPhuc::LOC_HINH_ANH_OPTIONS))],
        ]);

        $query = TrangPhuc::query()
            ->withCount([
                'sanPhamChoThue as luot_dung_thue',
                'hopDongCuoiTrangPhuc as luot_dung_cuoi',
            ]);

        $tuKhoa = trim((string) ($validated['tu_khoa'] ?? $validated['search'] ?? ''));
        if ($tuKhoa !== '') {
            $like = '%'.addcslashes($tuKhoa, '%_\\').'%';
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_san_pham', 'like', $like)
                    ->orWhere('ma_san_pham', 'like', $like)
                    ->orWhere('ngay_nhap', 'like', $like)
                    ->orWhere('ghi_chu', 'like', $like);
            });
        }

        $locHinhAnh = $validated['loc_hinh_anh'] ?? '';
        if ($locHinhAnh === TrangPhuc::LOC_HINH_ANH_CO) {
            $query->whereNotNull('hinh_anh')->where('hinh_anh', '<>', '');
        } elseif ($locHinhAnh === TrangPhuc::LOC_HINH_ANH_CHUA) {
            $query->where(function ($qb): void {
                $qb->whereNull('hinh_anh')->orWhere('hinh_anh', '');
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? TrangPhuc::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, TrangPhuc::SAP_XEP_OPTIONS)) {
            $sapXepTheo = TrangPhuc::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            TrangPhuc::SAP_XEP_TEN => $query->orderBy('ten_san_pham', $thuTu),
            TrangPhuc::SAP_XEP_MA => $query->orderBy('ma_san_pham', $thuTu),
            TrangPhuc::SAP_XEP_GIA_TRI => $query->orderBy('gia_tri', $thuTu),
            TrangPhuc::SAP_XEP_LUOT_DUNG => $query
                ->orderByRaw('(luot_dung_thue + luot_dung_cuoi) '.$thuTu)
                ->orderBy('id', $thuTu),
            TrangPhuc::SAP_XEP_TRANG_THAI => $query->orderBy('trang_thai', $thuTu),
            TrangPhuc::SAP_XEP_CREATED_AT => $query->orderBy('created_at', $thuTu),
            default => $query->orderBy('id', $thuTu),
        };

        $danhSach = $query->paginate(TrangPhuc::perPageSanPham($request))->withQueryString();

        return view('admin.trang-phuc.san-pham', compact('danhSach'));
    }

    public function storeSanPham(Request $request)
    {
        $validated = $request->validate([
            'ten_san_pham' => 'required|string|max:255',
            'ma_san_pham' => 'required|string|max:255|unique:trang_phuc,ma_san_pham',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'ngay_nhap' => 'nullable|string|max:255',
            'ghi_chu' => 'nullable|string|max:500',
            'gia_tri' => 'nullable|numeric|min:0',
        ]);

        $hinhAnhPath = null;
        if ($request->hasFile('hinh_anh')) {
            $hinhAnhPath = $request->file('hinh_anh')->store('trang-phuc/san-pham', 'public');
        }

        $ngayNhap = trim((string) ($validated['ngay_nhap'] ?? ''));

        TrangPhuc::create([
            'ten_san_pham' => $validated['ten_san_pham'],
            'ma_san_pham' => $validated['ma_san_pham'],
            'ngay_nhap' => $ngayNhap !== '' ? $ngayNhap : null,
            'hinh_anh' => $hinhAnhPath,
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'trang_thai' => TrangPhuc::TRANG_THAI_ACTIVE,
            'gia_tri' => $validated['gia_tri'] ?? 0,
        ]);

        return redirect()->route('admin.trang-phuc.san-pham')->with('success', 'Đã thêm sản phẩm trang phục thành công.');
    }

    /**
     * Import hàng loạt sản phẩm trang phục từ mảng JSON.
     *
     * @return JsonResponse
     */
    public function importSanPhamJson(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'required|array',
        ], [
            'items.required' => 'Vui lòng gửi mảng dữ liệu import.',
            'items.array' => 'Dữ liệu import phải là mảng JSON.',
            'items.min' => 'Mảng import phải có ít nhất 1 phần tử.',
        ]);

        $importThanhCong = [];
        $importThatBai = [];

        $maSanPhamDaTonTai = TrangPhuc::query()
            ->whereNotNull('ma_san_pham')
            ->pluck('ma_san_pham')
            ->map(static fn ($ma): string => mb_strtolower(trim((string) $ma)))
            ->flip()
            ->all();

        $maSanPhamTrongBatch = [];

        foreach ($validated['items'] as $index => $item) {
            if (! is_array($item)) {
                $importThatBai[] = [
                    'index' => $index,
                    'data' => $item,
                    'errors' => ['Phần tử phải là object JSON.'],
                ];

                continue;
            }

            $itemValidator = Validator::make($item, [
                'ma_san_pham' => 'required|string|max:255',
                'ten_san_pham' => 'required|string|max:255',
                'gia_tri' => 'nullable|numeric|min:0',
                'hinh_anh' => 'nullable|string|max:500',
                'ghi_chu' => 'nullable|string|max:500',
                'ngay_nhap' => 'nullable|string|max:255',
                'trang_thai' => 'nullable|integer|in:0,1',
            ], [
                'ma_san_pham.required' => 'Mã sản phẩm là bắt buộc.',
                'ten_san_pham.required' => 'Tên sản phẩm là bắt buộc.',
                'gia_tri.numeric' => 'Giá trị phải là số.',
                'gia_tri.min' => 'Giá trị không được âm.',
                'trang_thai.in' => 'Trạng thái chỉ được 0 hoặc 1.',
            ]);

            if ($itemValidator->fails()) {
                $importThatBai[] = [
                    'index' => $index,
                    'data' => $item,
                    'errors' => $itemValidator->errors()->all(),
                ];

                continue;
            }

            $row = $itemValidator->validated();
            $maSanPham = trim((string) $row['ma_san_pham']);
            $maKey = mb_strtolower($maSanPham);

            if (isset($maSanPhamDaTonTai[$maKey]) || isset($maSanPhamTrongBatch[$maKey])) {
                $importThatBai[] = [
                    'index' => $index,
                    'data' => $item,
                    'errors' => ['Mã sản phẩm đã tồn tại hoặc trùng trong danh sách import.'],
                ];

                continue;
            }

            $maSanPhamTrongBatch[$maKey] = true;

            $ngayNhap = trim((string) ($row['ngay_nhap'] ?? ''));
            $hinhAnh = trim((string) ($row['hinh_anh'] ?? ''));

            try {
                $sanPham = TrangPhuc::create([
                    'ten_san_pham' => trim((string) $row['ten_san_pham']),
                    'ma_san_pham' => $maSanPham,
                    'ngay_nhap' => $ngayNhap !== '' ? $ngayNhap : null,
                    'hinh_anh' => $hinhAnh !== '' ? $hinhAnh : null,
                    'ghi_chu' => filled($row['ghi_chu'] ?? null) ? trim((string) $row['ghi_chu']) : null,
                    'trang_thai' => array_key_exists('trang_thai', $row)
                        ? (int) $row['trang_thai']
                        : TrangPhuc::TRANG_THAI_ACTIVE,
                    'gia_tri' => $row['gia_tri'] ?? 0,
                ]);

                $maSanPhamDaTonTai[$maKey] = true;

                $importThanhCong[] = [
                    'index' => $index,
                    'id' => (int) $sanPham->id,
                    'ma_san_pham' => (string) $sanPham->ma_san_pham,
                    'ten_san_pham' => (string) $sanPham->ten_san_pham,
                ];
            } catch (\Throwable $e) {
                $importThatBai[] = [
                    'index' => $index,
                    'data' => $item,
                    'errors' => ['Không thể lưu dữ liệu: '.$e->getMessage()],
                ];
            }
        }

        return response()->json([
            'success' => true,
            'import_thanh_cong' => $importThanhCong,
            'import_that_bai' => $importThatBai,
            'tong' => count($validated['items']),
        ]);
    }

    public function updateSanPham(Request $request, TrangPhuc $trangPhuc)
    {
        $validated = $request->validate([
            'ten_san_pham' => 'required|string|max:255',
            'ma_san_pham' => 'required|string|max:255|unique:trang_phuc,ma_san_pham,'.$trangPhuc->id,
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'hinh_anh_duong_dan' => 'nullable|string|max:500',
            'ngay_nhap' => 'nullable|string|max:255',
            'ghi_chu' => 'nullable|string|max:500',
            'gia_tri' => 'nullable|numeric|min:0',
        ]);

        $ngayNhap = trim((string) ($validated['ngay_nhap'] ?? ''));

        $updateData = [
            'ten_san_pham' => $validated['ten_san_pham'],
            'ma_san_pham' => $validated['ma_san_pham'],
            'ngay_nhap' => $ngayNhap !== '' ? $ngayNhap : null,
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'gia_tri' => $validated['gia_tri'] ?? 0,
        ];

        if ($request->hasFile('hinh_anh')) {
            $newPath = $request->file('hinh_anh')->store('trang-phuc/san-pham', 'public');
            $updateData['hinh_anh'] = $newPath;

            if (! empty($trangPhuc->hinh_anh) && Storage::disk('public')->exists($trangPhuc->hinh_anh)) {
                Storage::disk('public')->delete($trangPhuc->hinh_anh);
            }
        } else {
            $duongDanHinhAnh = trim((string) ($validated['hinh_anh_duong_dan'] ?? ''));
            $updateData['hinh_anh'] = $duongDanHinhAnh !== '' ? $duongDanHinhAnh : null;
        }

        $trangPhuc->update($updateData);

        return redirect()->route('admin.trang-phuc.san-pham')->with('success', 'Đã cập nhật sản phẩm trang phục thành công.');
    }

    public function updateSanPhamTrangThai(Request $request, TrangPhuc $trangPhuc): JsonResponse
    {
        $validated = $request->validate([
            'trang_thai' => 'required|integer|in:0,1',
        ], [
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.integer' => 'Trạng thái phải là số nguyên.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $trangPhuc->update([
            'trang_thai' => (int) $validated['trang_thai'],
        ]);

        return response()->json([
            'success' => true,
            'trang_thai' => $trangPhuc->trang_thai,
            'message' => 'Đã cập nhật trạng thái sản phẩm.',
        ]);
    }

    public function destroySanPham(TrangPhuc $trangPhuc)
    {
        $trangPhuc->delete();

        return redirect()->route('admin.trang-phuc.san-pham')->with('success', 'Đã xóa sản phẩm trang phục.');
    }

    /**
     * Kiểm tra lịch sử/ngày sử dụng của 1 sản phẩm trang phục:
     * - HĐ cho thuê: từ ngay_thue đến (ngay_tra_chinh_thuc || ngay_tra_du_kien)
     * - HĐ cưới: dùng theo ngày chụp (ngay_chup_thuc_te || ngay_chup_du_kien)
     */
    public function kiemTraSuDungSanPham(TrangPhuc $trangPhuc): JsonResponse
    {
        $trangPhucId = (int) $trangPhuc->id;

        $thue = HopDongChoThueTrangPhuc::query()
            ->whereHas('sanPhamChoThue', function ($qb) use ($trangPhucId): void {
                $qb->where('san_pham_id', $trangPhucId);
            })
            ->orderByDesc('id')
            ->get(['id', 'ten_khach_hang', 'sdt_khach_hang', 'ngay_thue', 'ngay_tra_du_kien', 'ngay_tra_chinh_thuc', 'trang_thai'])
            ->map(static function (HopDongChoThueTrangPhuc $hd) {
                $start = $hd->ngay_thue ? Carbon::parse($hd->ngay_thue)->format('Y-m-d') : null;
                $endRaw = $hd->ngay_tra_chinh_thuc ?? $hd->ngay_tra_du_kien;
                $end = $endRaw ? Carbon::parse($endRaw)->format('Y-m-d') : null;

                return [
                    'hop_dong_id' => (int) $hd->id,
                    'loai' => 'thue',
                    'tu_ngay' => $start,
                    'den_ngay' => $end,
                    'trang_thai' => (int) ($hd->trang_thai ?? 0),
                    'khach_hang' => [
                        'ten' => (string) ($hd->ten_khach_hang ?? ''),
                        'sdt' => (string) ($hd->sdt_khach_hang ?? ''),
                    ],
                ];
            })
            ->values()
            ->all();

        $cuoiRows = HopDongCuoiTrangPhuc::query()
            ->where('trang_phuc_id', $trangPhucId)
            ->orderByDesc('id')
            ->pluck('hop_dong_cuoi_id')
            ->unique()
            ->values()
            ->all();

        $cuoi = HopDongCuoi::query()
            ->whereIn('id', $cuoiRows)
            ->orderByDesc('id')
            ->get(['id', 'ma_hop_dong', 'ten_co_dau', 'ten_chu_re', 'ngay_chup_du_kien', 'ngay_chup_thuc_te'])
            ->map(static function (HopDongCuoi $hd) {
                $d = $hd->ngay_chup_thuc_te ?? $hd->ngay_chup_du_kien;
                $ngay = $d ? Carbon::parse($d)->format('Y-m-d') : null;

                return [
                    'hop_dong_id' => (int) $hd->id,
                    'loai' => 'cuoi',
                    'ngay' => $ngay,
                    'ma_hop_dong' => (string) ($hd->ma_hop_dong ?? ''),
                    'cap_doi' => trim((string) ($hd->ten_co_dau ?? '').' - '.(string) ($hd->ten_chu_re ?? ''), ' -'),
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'trang_phuc' => [
                'id' => $trangPhucId,
                'ten' => (string) ($trangPhuc->ten_san_pham ?? ''),
                'ma' => (string) ($trangPhuc->ma_san_pham ?? ''),
            ],
            'thue' => $thue,
            'cuoi' => $cuoi,
        ]);
    }

    /**
     * Tìm sản phẩm trang phục (JSON) cho modal thêm hợp đồng thuê.
     */
    public function timSanPhamHopDong(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
        ]);

        $needle = trim((string) ($validated['q'] ?? ''));

        $query = TrangPhuc::query()
            ->where('trang_thai', TrangPhuc::TRANG_THAI_ACTIVE)
            ->orderByDesc('id')
            ->limit(80);

        if ($needle !== '') {
            $query->where(function ($qb) use ($needle) {
                $qb->where('ten_san_pham', 'like', '%'.$needle.'%')
                    ->orWhere('ma_san_pham', 'like', '%'.$needle.'%');
            });
        }

        $rows = $query->get(['id', 'ten_san_pham', 'ma_san_pham', 'hinh_anh', 'gia_tri', 'ngay_nhap']);
        $coLichSuIds = $this->cacSanPhamIdCoLichSuSuDung(
            $rows->pluck('id')->map(static fn ($id): int => (int) $id)->all()
        );

        $items = [];
        foreach ($rows as $sp) {
            $items[] = $this->buildSanPhamCatalogEntry($sp, isset($coLichSuIds[(int) $sp->id]));
        }

        return response()->json(['items' => $items]);
    }

    /**
     * Catalog sản phẩm trang phục cho wizard HĐ cưới (tách theo loại chụp / cưới).
     *
     * @return array{chup: list<array<string, mixed>>, cuoi: list<array<string, mixed>>}
     */
    public function sanPhamCatalogChoWizardHopDongCuoi(bool $dungChungTrangPhuc = true): array
    {
        $items = TrangPhuc::query()
            ->where('trang_thai', TrangPhuc::TRANG_THAI_ACTIVE)
            ->orderBy('ten_san_pham')
            ->get(['id', 'ten_san_pham', 'ma_san_pham', 'hinh_anh', 'gia_tri', 'ngay_nhap']);

        $chup = [];
        $cuoi = [];
        $coLichSuIds = $this->cacSanPhamIdCoLichSuSuDung(
            $items->pluck('id')->map(static fn ($id): int => (int) $id)->all()
        );

        foreach ($items as $sp) {
            $entry = $this->buildSanPhamCatalogEntry($sp, isset($coLichSuIds[(int) $sp->id]));
            $chup[] = $entry;
            $cuoi[] = $entry;
        }

        return ['chup' => $chup, 'cuoi' => $cuoi];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSanPhamCatalogEntry(TrangPhuc $sp, bool $coLichSuSuDung = false): array
    {
        $id = (int) $sp->id;
        $hinhPath = $sp->hinh_anh ?? null;

        return [
            'id' => $id,
            'ten' => (string) ($sp->ten_san_pham ?? ''),
            'ma' => (string) ($sp->ma_san_pham ?? ''),
            'ngay_nhap' => filled($sp->ngay_nhap) ? (string) $sp->ngay_nhap : null,
            'hinh_anh_url' => $hinhPath ? '/storage/'.ltrim($hinhPath, '/') : '',
            'gia_tri' => $sp->gia_tri !== null ? (float) $sp->gia_tri : null,
            'kiem_tra_url' => route('admin.trang-phuc.san-pham.kiem-tra', $sp),
            'stock' => $this->tonKhoKhaDungChoThue($id),
            'sdDates' => array_values(array_map('strval', $this->cacNgayTraDuKienDangThueTuHomNay($id))),
            'coLichSuSuDung' => $coLichSuSuDung,
        ];
    }

    /**
     * @param  list<int>  $trangPhucIds
     * @return array<int, true>
     */
    private function cacSanPhamIdCoLichSuSuDung(array $trangPhucIds): array
    {
        $trangPhucIds = array_values(array_unique(array_filter(array_map('intval', $trangPhucIds), static fn (int $id): bool => $id > 0)));
        if ($trangPhucIds === []) {
            return [];
        }

        $tuThue = SanPhamChoThue::query()
            ->whereIn('san_pham_id', $trangPhucIds)
            ->distinct()
            ->pluck('san_pham_id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        $tuCuoi = HopDongCuoiTrangPhuc::query()
            ->whereIn('trang_phuc_id', $trangPhucIds)
            ->distinct()
            ->pluck('trang_phuc_id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        $map = [];
        foreach (array_merge($tuThue, $tuCuoi) as $id) {
            $map[$id] = true;
        }

        return $map;
    }

    public function hopDong(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:200',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(HopDongChoThueTrangPhuc::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = HopDongChoThueTrangPhuc::query()
            ->with(['nguoiChoThue', 'sanPhamChoThue.sanPham']);

        $search = trim((string) ($validated['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_khach_hang', 'like', $like)
                    ->orWhere('sdt_khach_hang', 'like', $like);
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? HopDongChoThueTrangPhuc::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, HopDongChoThueTrangPhuc::SAP_XEP_OPTIONS)) {
            $sapXepTheo = HopDongChoThueTrangPhuc::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            HopDongChoThueTrangPhuc::SAP_XEP_TEN_KHACH => $query->orderBy('ten_khach_hang', $thuTu),
            HopDongChoThueTrangPhuc::SAP_XEP_SDT => $query->orderBy('sdt_khach_hang', $thuTu),
            HopDongChoThueTrangPhuc::SAP_XEP_SO_NGAY_THUE => $query->orderBy('so_ngay_thue', $thuTu),
            HopDongChoThueTrangPhuc::SAP_XEP_TONG_TIEN => $query->orderBy('tong_tien', $thuTu),
            HopDongChoThueTrangPhuc::SAP_XEP_NGAY_THUE => $query->orderBy('ngay_thue', $thuTu),
            HopDongChoThueTrangPhuc::SAP_XEP_NGAY_TRA_DU_KIEN => $query->orderBy('ngay_tra_du_kien', $thuTu),
            HopDongChoThueTrangPhuc::SAP_XEP_NGAY_TRA_CHINH_THUC => $query->orderBy('ngay_tra_chinh_thuc', $thuTu),
            HopDongChoThueTrangPhuc::SAP_XEP_TRANG_THAI => $query->orderBy('trang_thai', $thuTu),
            HopDongChoThueTrangPhuc::SAP_XEP_CREATED_AT => $query->orderBy('created_at', $thuTu),
            default => $query->orderBy('id', $thuTu),
        };

        $danhSach = $query->paginate(AdminPagination::perPage())->withQueryString();

        $danhSachSanPham = TrangPhuc::query()->orderBy('ten_san_pham')->get();

        $stockByProduct = [];
        $trangPhucSuDungTuHomNay = [];
        foreach ($danhSachSanPham as $sp) {
            $id = (int) $sp->id;
            $stockByProduct[$id] = $this->tonKhoKhaDungChoThue($id);
            $trangPhucSuDungTuHomNay[$id] = $this->cacNgayTraDuKienDangThueTuHomNay($id);
        }

        $lichChoThueHopDong = $this->lichChoThueHopDongChoJs();

        return view('admin.trang-phuc.hop-dong', compact(
            'danhSach',
            'danhSachSanPham',
            'stockByProduct',
            'trangPhucSuDungTuHomNay',
            'lichChoThueHopDong',
        ));
    }

    public function storeHopDong(Request $request)
    {
        $validated = $request->validate([
            'ten_khach_hang' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:20',
            'trang_phuc' => 'required|array|min:1',
            'trang_phuc.*' => 'integer|exists:trang_phuc,id',
            'tong_tien' => 'required|numeric|min:0',
            'thoi_gian_thue_bat_dau' => 'required|date',
            'thoi_gian_du_kien_tra' => 'required|date|after_or_equal:thoi_gian_thue_bat_dau',
            'ghi_chu' => 'nullable|string',
            'tien_coc' => 'nullable|numeric|min:0',
        ]);

        $productIds = array_values(array_unique(array_map('intval', $validated['trang_phuc'])));
        $ngayBatDau = Carbon::parse($validated['thoi_gian_thue_bat_dau'])->startOfDay();
        $ngayDuKienTra = Carbon::parse($validated['thoi_gian_du_kien_tra'])->startOfDay();

        foreach ($productIds as $trangPhucId) {
            if ($this->tonKhoKhaDungChoThue($trangPhucId, null, $ngayBatDau, $ngayDuKienTra) < 1) {
                return redirect()->back()
                    ->withErrors(['trang_phuc' => 'Sản phẩm id '.$trangPhucId.' đang được cho thuê trong khoảng thời gian này, không thể thêm vào hợp đồng.'])
                    ->withInput();
            }
        }
        $tongTien = round((float) $validated['tong_tien'], 2);
        $tienCoc = round((float) ($validated['tien_coc'] ?? 0), 2);

        DB::transaction(function () use (
            $validated,
            $request,
            $productIds,
            $ngayBatDau,
            $ngayDuKienTra,
            $tongTien,
            $tienCoc,
        ): void {
            $hopDong = HopDongChoThueTrangPhuc::create([
                'ten_khach_hang' => $validated['ten_khach_hang'],
                'sdt_khach_hang' => $validated['so_dien_thoai'],
                'ngay_thue' => $ngayBatDau,
                'ngay_tra_du_kien' => $ngayDuKienTra,
                'ngay_tra_chinh_thuc' => null,
                'tong_tien' => $tongTien,
                'tien_coc' => $tienCoc,
                'ghi_chu' => $validated['ghi_chu'] ?? null,
                'trang_thai' => 0,
                'nguoi_cho_thue' => $request->user()?->id,
            ]);

            foreach ($productIds as $trangPhucId) {
                SanPhamChoThue::create([
                    'hop_dong_id' => $hopDong->id,
                    'san_pham_id' => $trangPhucId,
                ]);
            }
        });

        return redirect()->route('admin.trang-phuc.hop-dong')->with('success', 'Đã thêm hợp đồng thành công.');
    }

    public function updateHopDong(Request $request, HopDongChoThueTrangPhuc $hopDong)
    {
        if ((int) $hopDong->trang_thai !== 0) {
            return redirect()->route('admin.trang-phuc.hop-dong')
                ->with('error', 'Không thể sửa hợp đồng đã hoàn thành hoặc đã huỷ.');
        }

        if ($request->input('thoi_gian_tra_hang_chinh_thuc') === '') {
            $request->merge(['thoi_gian_tra_hang_chinh_thuc' => null]);
        }

        $validated = $request->validate([
            'ten_khach_hang' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:20',
            'trang_phuc' => 'required|array|min:1',
            'trang_phuc.*' => 'integer|exists:trang_phuc,id',
            'tong_tien' => 'required|numeric|min:0',
            'thoi_gian_thue_bat_dau' => 'required|date',
            'thoi_gian_du_kien_tra' => 'required|date|after_or_equal:thoi_gian_thue_bat_dau',
            'thoi_gian_tra_hang_chinh_thuc' => 'nullable|date',
            'ghi_chu' => 'nullable|string',
            'tien_coc' => 'nullable|numeric|min:0',
            'trang_thai' => 'nullable|integer|in:0,1,2',
        ]);

        $productIds = array_values(array_unique(array_map('intval', $validated['trang_phuc'])));
        $ngayBatDau = Carbon::parse($validated['thoi_gian_thue_bat_dau'])->startOfDay();
        $ngayDuKienTra = Carbon::parse($validated['thoi_gian_du_kien_tra'])->startOfDay();

        foreach ($productIds as $trangPhucId) {
            if ($this->tonKhoKhaDungChoThue($trangPhucId, (int) $hopDong->id, $ngayBatDau, $ngayDuKienTra) < 1) {
                return redirect()->back()
                    ->withErrors(['trang_phuc' => 'Sản phẩm id '.$trangPhucId.' đang được cho thuê trong khoảng thời gian này, không thể thêm vào hợp đồng.'])
                    ->withInput();
            }
        }
        $tongTien = round((float) $validated['tong_tien'], 2);
        $tienCoc = round((float) ($validated['tien_coc'] ?? 0), 2);

        DB::transaction(function () use (
            $validated,
            $hopDong,
            $productIds,
            $ngayBatDau,
            $ngayDuKienTra,
            $tongTien,
            $tienCoc,
        ): void {
            $hopDong->update([
                'ten_khach_hang' => $validated['ten_khach_hang'],
                'sdt_khach_hang' => $validated['so_dien_thoai'],
                'ngay_thue' => $ngayBatDau,
                'ngay_tra_du_kien' => $ngayDuKienTra,
                'ngay_tra_chinh_thuc' => ! empty($validated['thoi_gian_tra_hang_chinh_thuc'])
                    ? Carbon::parse($validated['thoi_gian_tra_hang_chinh_thuc'])->startOfDay()
                    : null,
                'tong_tien' => $tongTien,
                'tien_coc' => $tienCoc,
                'ghi_chu' => $validated['ghi_chu'] ?? null,
                'trang_thai' => isset($validated['trang_thai']) ? (int) $validated['trang_thai'] : $hopDong->trang_thai,
            ]);

            $hopDong->sanPhamChoThue()->delete();

            foreach ($productIds as $trangPhucId) {
                SanPhamChoThue::create([
                    'hop_dong_id' => $hopDong->id,
                    'san_pham_id' => $trangPhucId,
                ]);
            }
        });

        return redirect()->route('admin.trang-phuc.hop-dong')->with('success', 'Đã cập nhật hợp đồng thành công.');
    }

    public function updateHopDongTrangThai(Request $request, HopDongChoThueTrangPhuc $hopDong)
    {
        if ($request->input('ngay_tra_chinh_thuc') === '') {
            $request->merge(['ngay_tra_chinh_thuc' => null]);
        }

        $validated = $request->validate([
            'trang_thai' => 'required|integer|in:1,2',
            'ngay_tra_chinh_thuc' => 'nullable|date',
            'tien_coc' => 'nullable|numeric|min:0',
        ]);

        $trangThai = (int) $validated['trang_thai'];
        $ngayTraChinhThuc = $trangThai === 2
            ? null
            : (! empty($validated['ngay_tra_chinh_thuc'])
                ? Carbon::parse($validated['ngay_tra_chinh_thuc'])->startOfDay()
                : $hopDong->ngay_tra_chinh_thuc);

        $tongTien = round((float) $hopDong->tong_tien, 2);
        $tienCoc = round((float) ($validated['tien_coc'] ?? $hopDong->tien_coc ?? 0), 2);
        if ($tongTien > 0 && $tienCoc > $tongTien) {
            return redirect()->route('admin.trang-phuc.hop-dong')
                ->withErrors(['tien_coc' => 'Đã thanh toán không được lớn hơn tổng tiền hợp đồng.'])
                ->withInput();
        }

        $hopDong->update([
            'trang_thai' => $trangThai,
            'ngay_tra_chinh_thuc' => $ngayTraChinhThuc,
            'tien_coc' => $tienCoc,
        ]);

        $msg = $trangThai === 2 ? 'Đã huỷ hợp đồng.' : 'Đã chuyển hợp đồng sang Hoàn thành.';

        return redirect()->route('admin.trang-phuc.hop-dong')->with('success', $msg);
    }

    public function destroyHopDong(HopDongChoThueTrangPhuc $hopDong)
    {
        $hopDong->delete();

        return redirect()->route('admin.trang-phuc.hop-dong')->with('success', 'Đã xóa hợp đồng.');
    }

    private function trangPhucHinhAnhPublicUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * Số đơn vị còn cho thuê: mỗi sản phẩm tối đa 1.
     * Chỉ tính HĐ còn hiệu lực (trạng thái 0); nếu có khoảng ngày thì chỉ HĐ trùng thời gian thuê.
     */
    private function tonKhoKhaDungChoThue(
        int $trangPhucId,
        ?int $excludeHopDongId = null,
        ?Carbon $ngayBatDau = null,
        ?Carbon $ngayKetThuc = null,
    ): int {
        $q = SanPhamChoThue::query()
            ->where('san_pham_id', $trangPhucId)
            ->whereHas('hopDong', function ($qb) use ($excludeHopDongId, $ngayBatDau, $ngayKetThuc): void {
                $qb->where('trang_thai', 0);
                if ($excludeHopDongId !== null) {
                    $qb->where('id', '<>', $excludeHopDongId);
                }
                if ($ngayBatDau !== null && $ngayKetThuc !== null) {
                    $batDau = $ngayBatDau->copy()->startOfDay()->format('Y-m-d');
                    $ketThuc = $ngayKetThuc->copy()->startOfDay()->format('Y-m-d');
                    // Trùng khoảng: ngay_thue <= ketThuc và ngày kết thúc HĐ >= batDau
                    $qb->whereDate('ngay_thue', '<=', $ketThuc)
                        ->where(function ($q2) use ($batDau): void {
                            $q2->whereDate('ngay_tra_chinh_thuc', '>=', $batDau)
                                ->orWhere(function ($q3) use ($batDau): void {
                                    $q3->whereNull('ngay_tra_chinh_thuc')
                                        ->whereDate('ngay_tra_du_kien', '>=', $batDau);
                                });
                        });
                }
            });
        $dangThue = (int) $q->count();

        return max(0, self::SO_LUONG_TINH_TE_TOI_DA - $dangThue);
    }

    /**
     * Lịch HĐ đang thuê (trạng thái 0) + mã SP — dùng JS lọc SP trùng khoảng ngày trên modal thêm HĐ.
     *
     * @return list<array{tu: string, den: string, ma_san_pham: list<string>}>
     */
    private function lichChoThueHopDongChoJs(): array
    {
        return HopDongChoThueTrangPhuc::query()
            ->where('trang_thai', 0)
            ->with(['sanPhamChoThue.sanPham:id,ma_san_pham'])
            ->orderBy('id')
            ->get(['id', 'ngay_thue', 'ngay_tra_du_kien', 'ngay_tra_chinh_thuc'])
            ->map(static function (HopDongChoThueTrangPhuc $hd): array {
                $tu = $hd->ngay_thue
                    ? Carbon::parse($hd->ngay_thue)->format('Y-m-d')
                    : '';
                $endRaw = $hd->ngay_tra_chinh_thuc ?? $hd->ngay_tra_du_kien;
                $den = $endRaw
                    ? Carbon::parse($endRaw)->format('Y-m-d')
                    : '';
                $maSanPham = [];
                foreach ($hd->sanPhamChoThue as $dong) {
                    $ma = $dong->sanPham?->ma_san_pham;
                    if ($ma !== null && $ma !== '') {
                        $maSanPham[] = (string) $ma;
                    }
                }

                return [
                    'tu' => $tu,
                    'den' => $den,
                    'ma_san_pham' => array_values(array_unique($maSanPham)),
                ];
            })
            ->filter(static fn (array $row): bool => $row['tu'] !== '' && $row['den'] !== '')
            ->values()
            ->all();
    }

    /**
     * Các ngày dự kiến trả của hợp đồng còn hiệu lực (từ hôm nay trở đi).
     *
     * @return list<string>
     */
    private function cacNgayTraDuKienDangThueTuHomNay(int $trangPhucId): array
    {
        $today = Carbon::today();

        return HopDongChoThueTrangPhuc::query()
            ->where('trang_thai', 0)
            ->whereDate('ngay_tra_du_kien', '>=', $today)
            ->whereHas('sanPhamChoThue', function ($qb) use ($trangPhucId): void {
                $qb->where('san_pham_id', $trangPhucId);
            })
            ->orderBy('ngay_tra_du_kien')
            ->pluck('ngay_tra_du_kien')
            ->map(static function ($d) {
                return $d instanceof Carbon
                    ? $d->format('Y-m-d')
                    : Carbon::parse($d)->format('Y-m-d');
            })
            ->unique()
            ->values()
            ->all();
    }
}

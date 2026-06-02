<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NganHangThanhToan;
use App\Models\NhanVien;
use App\Models\PhongBan;
use App\Models\TaiLieu;
use App\Models\User;
use App\Models\VaiTro;
use App\Support\AdminMenuPermissions;
use App\Support\AdminPagination;
use App\Support\UserActionLogReader;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HeThongController extends Controller
{
    /**
     * Trang danh sách ngân hàng thanh toán (Read trong CRUD).
     */
    public function nganHangThanhToan(Request $request)
    {
        $query = NganHangThanhToan::query();

        // Lọc theo từ khóa: tên ngân hàng / số tài khoản / chủ tài khoản.
        if ($request->filled('search')) {
            $q = trim((string) $request->input('search'));
            $query->where(function ($qb) use ($q) {
                $qb->where('ten_ngan_hang', 'like', '%'.$q.'%')
                    ->orWhere('so_tai_khoan', 'like', '%'.$q.'%')
                    ->orWhere('chu_tai_khoan', 'like', '%'.$q.'%');
            });
        }

        // Lọc theo trạng thái nếu người dùng chọn.
        if ($request->filled('trang_thai') && in_array((string) $request->input('trang_thai'), ['0', '1'], true)) {
            $query->where('trang_thai', (int) $request->input('trang_thai'));
        }

        $danhSach = $query
            ->orderByDesc('id')
            ->paginate(AdminPagination::perPage())->withQueryString()
            ->withQueryString();

        return view('admin.he-thong.ngan-hang-thanh-toan', compact('danhSach'));
    }

    /**
     * Tạo mới ngân hàng thanh toán (Create trong CRUD).
     */
    public function storeNganHangThanhToan(Request $request)
    {
        $request->validate([
            'hinh_anh_logo' => 'nullable|string|max:500',
            'ten_ngan_hang' => 'required|string|max:150',
            'ten_chi_tiet' => 'nullable|string|max:255',
            'so_tai_khoan' => 'required|string|max:50',
            'chu_tai_khoan' => 'required|string|max:150',
            'chi_nhanh' => 'nullable|string|max:255',
            'trang_thai' => 'required|in:0,1',
        ], [
            'ten_ngan_hang.required' => 'Vui lòng nhập tên ngân hàng.',
            'so_tai_khoan.required' => 'Vui lòng nhập số tài khoản.',
            'chu_tai_khoan.required' => 'Vui lòng nhập chủ tài khoản.',
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ]);

        NganHangThanhToan::create([
            'hinh_anh_logo' => $request->input('hinh_anh_logo'),
            'ten_ngan_hang' => $request->input('ten_ngan_hang'),
            'ten_chi_tiet' => $request->input('ten_chi_tiet'),
            'so_tai_khoan' => $request->input('so_tai_khoan'),
            'chu_tai_khoan' => $request->input('chu_tai_khoan'),
            'chi_nhanh' => $request->input('chi_nhanh'),
            'trang_thai' => (int) $request->input('trang_thai', 1),
        ]);

        return redirect()
            ->route('admin.he-thong.ngan-hang-thanh-toan')
            ->with('success', 'Đã thêm ngân hàng thanh toán thành công.');
    }

    /**
     * Cập nhật ngân hàng thanh toán (Update trong CRUD).
     */
    public function updateNganHangThanhToan(Request $request, NganHangThanhToan $nganHangThanhToan)
    {
        $request->validate([
            'hinh_anh_logo' => 'nullable|string|max:500',
            'ten_ngan_hang' => 'required|string|max:150',
            'ten_chi_tiet' => 'nullable|string|max:255',
            'so_tai_khoan' => 'required|string|max:50',
            'chu_tai_khoan' => 'required|string|max:150',
            'chi_nhanh' => 'nullable|string|max:255',
            'trang_thai' => 'required|in:0,1',
        ], [
            'ten_ngan_hang.required' => 'Vui lòng nhập tên ngân hàng.',
            'so_tai_khoan.required' => 'Vui lòng nhập số tài khoản.',
            'chu_tai_khoan.required' => 'Vui lòng nhập chủ tài khoản.',
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $nganHangThanhToan->update([
            'hinh_anh_logo' => $request->input('hinh_anh_logo'),
            'ten_ngan_hang' => $request->input('ten_ngan_hang'),
            'ten_chi_tiet' => $request->input('ten_chi_tiet'),
            'so_tai_khoan' => $request->input('so_tai_khoan'),
            'chu_tai_khoan' => $request->input('chu_tai_khoan'),
            'chi_nhanh' => $request->input('chi_nhanh'),
            'trang_thai' => (int) $request->input('trang_thai'),
        ]);

        return redirect()
            ->route('admin.he-thong.ngan-hang-thanh-toan')
            ->with('success', 'Đã cập nhật ngân hàng thanh toán thành công.');
    }

    /**
     * Xoá ngân hàng thanh toán (Delete trong CRUD).
     */
    public function destroyNganHangThanhToan(NganHangThanhToan $nganHangThanhToan)
    {
        $nganHangThanhToan->delete();

        return redirect()
            ->route('admin.he-thong.ngan-hang-thanh-toan')
            ->with('success', 'Đã xoá ngân hàng thanh toán.');
    }

    public function vaiTro(Request $request)
    {
        $validated = $request->validate([
            'tu_khoa' => 'nullable|string|max:200',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(VaiTro::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = VaiTro::query()->withCount('users');

        $tuKhoa = trim((string) ($validated['tu_khoa'] ?? $request->query('search', '')));
        if ($tuKhoa !== '') {
            $like = '%'.addcslashes($tuKhoa, '%_\\').'%';
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_vai_tro', 'like', $like)
                    ->orWhere('mo_ta', 'like', $like)
                    ->orWhere('ghi_chu', 'like', $like);
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? VaiTro::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, VaiTro::SAP_XEP_OPTIONS)) {
            $sapXepTheo = VaiTro::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            VaiTro::SAP_XEP_MA => $query->orderBy('ma_vai_tro', $thuTu),
            VaiTro::SAP_XEP_TEN => $query->orderBy('ten_vai_tro', $thuTu),
            VaiTro::SAP_XEP_USERS => $query->orderBy('users_count', $thuTu),
            VaiTro::SAP_XEP_MO_TA => $query
                ->orderByRaw('mo_ta IS NULL')
                ->orderBy('mo_ta', $thuTu),
            VaiTro::SAP_XEP_GHI_CHU => $query
                ->orderByRaw('ghi_chu IS NULL')
                ->orderBy('ghi_chu', $thuTu),
            VaiTro::SAP_XEP_CREATED_AT => $query->orderBy('created_at', $thuTu),
            default => $query->orderBy('id', $thuTu),
        };
        $query->orderBy('id', $thuTu);

        $danhSach = $query->paginate(AdminPagination::perPage())->withQueryString();
        $adminGetRoutes = AdminMenuPermissions::routesForForm();

        return view('admin.he-thong.vai-tro', compact('danhSach', 'adminGetRoutes'));
    }

    public function storeVaiTro(Request $request)
    {
        $request->validate([
            'ma_vai_tro' => ['required', 'string', 'max:50', 'regex:/^\d+$/', Rule::unique('vai_tro', 'ma_vai_tro')],
            'ten_vai_tro' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:255',
        ], [
            'ma_vai_tro.required' => 'Vui lòng nhập mã vai trò.',
            'ma_vai_tro.string' => 'Mã vai trò phải là chuỗi ký tự.',
            'ma_vai_tro.max' => 'Mã vai trò không được quá 50 ký tự.',
            'ma_vai_tro.regex' => 'Mã vai trò phải là số.',
            'ma_vai_tro.unique' => 'Mã vai trò đã tồn tại, vui lòng chọn mã khác.',
            'ten_vai_tro.required' => 'Vui lòng nhập tên vai trò.',
            'ten_vai_tro.string' => 'Tên vai trò phải là chuỗi ký tự.',
            'ten_vai_tro.max' => 'Tên vai trò không được quá 255 ký tự.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự.',
            'permissions.array' => 'Danh sách menu không hợp lệ.',
            'permissions.*.string' => 'Mỗi menu phải là chuỗi ký tự.',
            'permissions.*.max' => 'Mỗi menu không được quá 255 ký tự.',
        ]);

        VaiTro::create([
            'ma_vai_tro' => $request->input('ma_vai_tro'),
            'ten_vai_tro' => $request->input('ten_vai_tro'),
            'mo_ta' => $request->input('mo_ta'),
            'ghi_chu' => $request->input('ghi_chu'),
            'ds_menu' => AdminMenuPermissions::buildDsMenu($request->input('permissions')),
        ]);

        return redirect()->route('admin.he-thong.vai-tro')->with('success', 'Đã thêm vai trò thành công.');
    }

    public function updateVaiTro(Request $request, VaiTro $vaiTro)
    {
        $request->merge(['ma_vai_tro' => $vaiTro->ma_vai_tro]);

        $request->validate([
            'ten_vai_tro' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:255',
            'vai_tro_id' => 'nullable|integer',
        ], [
            'ten_vai_tro.required' => 'Vui lòng nhập tên vai trò.',
            'ten_vai_tro.string' => 'Tên vai trò phải là chuỗi ký tự.',
            'ten_vai_tro.max' => 'Tên vai trò không được quá 255 ký tự.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự.',
            'permissions.array' => 'Danh sách menu không hợp lệ.',
            'permissions.*.string' => 'Mỗi menu phải là chuỗi ký tự.',
            'permissions.*.max' => 'Mỗi menu không được quá 255 ký tự.',
        ]);

        $vaiTro->update([
            'ten_vai_tro' => $request->input('ten_vai_tro'),
            'mo_ta' => $request->input('mo_ta'),
            'ghi_chu' => $request->input('ghi_chu'),
            'ds_menu' => AdminMenuPermissions::buildDsMenu(
                $request->input('permissions'),
                $vaiTro->ds_menu ?? []
            ),
        ]);

        return redirect()->route('admin.he-thong.vai-tro')->with('success', 'Đã cập nhật vai trò thành công.');
    }

    public function destroyVaiTro(VaiTro $vaiTro)
    {
        if ($vaiTro->users()->exists()) {
            return redirect()
                ->route('admin.he-thong.vai-tro')
                ->with('error', 'Đang có người dùng gắn với vai trò này. Không thể xoá.');
        }

        $vaiTro->delete();

        return redirect()->route('admin.he-thong.vai-tro')->with('success', 'Đã xóa vai trò.');
    }

    /**
     * Danh sách người dùng theo vai trò (JSON cho modal).
     */
    public function nguoiDungVaiTro(VaiTro $vaiTro): JsonResponse
    {
        $items = $vaiTro->users()
            ->with(['nhanVien.phongBans'])
            ->orderBy('id')
            ->get()
            ->map(static function (User $user) use ($vaiTro) {
                $nv = $user->nhanVien;
                $phongBan = $nv?->phongBans
                    ? $nv->phongBans->pluck('ten_phong_ban')->filter()->implode(', ')
                    : '';

                return [
                    'id' => (int) $user->id,
                    'hinh_anh' => $nv?->hinh_anh ? asset('storage/'.$nv->hinh_anh) : null,
                    'name' => $user->name ?? '—',
                    'email' => $user->email ?? '',
                    'phone' => $user->phone ?? '',
                    'role_label' => $vaiTro->ten_vai_tro ?? '—',
                    'phong_ban' => $phongBan !== '' ? $phongBan : '—',
                    'gioi_tinh' => $nv?->gioi_tinh ?? '',
                    'ngay_sinh' => $nv?->ngay_sinh?->format('d/m/Y') ?? '',
                    'cccd' => $nv?->cccd ?? '',
                    'vi_tri_lam_viec' => $nv?->vi_tri_lam_viec ?? '',
                    'ngay_vao_cong_ty' => $nv?->ngay_vao_cong_ty?->format('d/m/Y') ?? '',
                    'ngay_ky_hop_dong' => $nv?->ngay_ky_hop_dong?->format('d/m/Y') ?? '',
                    'luong_co_ban' => $nv && $nv->luong_co_ban !== null ? number_format($nv->luong_co_ban) : '',
                    'luong_tang_ca' => $nv && $nv->luong_tang_ca !== null ? number_format($nv->luong_tang_ca) : '',
                    'ngan_hang' => $nv?->ngan_hang ?? '',
                    'chi_nhanh' => $nv?->chi_nhanh ?? '',
                    'so_tai_khoan' => $nv?->so_tai_khoan ?? '',
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'vai_tro' => [
                'id' => (int) $vaiTro->id,
                'ten_vai_tro' => $vaiTro->ten_vai_tro,
                'ma_vai_tro' => $vaiTro->ma_vai_tro,
            ],
            'items' => $items,
            'total' => count($items),
        ]);
    }

    public function phongBan(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(PhongBan::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = PhongBan::query()->withCount('nhanViens');

        $search = trim((string) ($validated['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_phong_ban', 'like', $like)
                    ->orWhere('ma_phong_ban', 'like', $like);
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? PhongBan::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, PhongBan::SAP_XEP_OPTIONS)) {
            $sapXepTheo = PhongBan::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            PhongBan::SAP_XEP_NHAN_VIENS => $query->orderBy('nhan_viens_count', $thuTu),
            default => $query->orderBy('id', $thuTu),
        };
        $query->orderBy('id', $thuTu);

        $danhSach = $query->paginate(AdminPagination::perPage())->withQueryString();

        return view('admin.he-thong.phong-ban', compact('danhSach'));
    }

    public function storePhongBan(Request $request)
    {
        $request->validate([
            'ten_phong_ban' => 'required|string|max:255',
            'ma_phong_ban' => ['required', 'string', 'max:50', Rule::unique('phong_ban', 'ma_phong_ban')],
            'mo_ta' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
        ], [
            'ten_phong_ban.required' => 'Vui lòng nhập tên phòng ban.',
            'ten_phong_ban.string' => 'Tên phòng ban phải là chuỗi ký tự.',
            'ten_phong_ban.max' => 'Tên phòng ban không được quá 255 ký tự.',
            'ma_phong_ban.required' => 'Vui lòng nhập mã phòng ban.',
            'ma_phong_ban.string' => 'Mã phòng ban phải là chuỗi ký tự.',
            'ma_phong_ban.max' => 'Mã phòng ban không được quá 50 ký tự.',
            'ma_phong_ban.unique' => 'Mã phòng ban đã tồn tại, vui lòng chọn mã khác.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự.',
        ]);

        PhongBan::create([
            'ten_phong_ban' => $request->input('ten_phong_ban'),
            'ma_phong_ban' => $request->input('ma_phong_ban'),
            'mo_ta' => $request->input('mo_ta'),
            'ghi_chu' => $request->input('ghi_chu'),
        ]);

        return redirect()->route('admin.he-thong.phong-ban')->with('success', 'Đã thêm phòng ban thành công.');
    }

    public function updatePhongBan(Request $request, PhongBan $phongBan)
    {
        $request->validate([
            'ten_phong_ban' => 'required|string|max:255',
            'ma_phong_ban' => ['required', 'string', 'max:50', Rule::unique('phong_ban', 'ma_phong_ban')->ignore($phongBan->id)],
            'mo_ta' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
        ], [
            'ten_phong_ban.required' => 'Vui lòng nhập tên phòng ban.',
            'ten_phong_ban.string' => 'Tên phòng ban phải là chuỗi ký tự.',
            'ten_phong_ban.max' => 'Tên phòng ban không được quá 255 ký tự.',
            'ma_phong_ban.required' => 'Vui lòng nhập mã phòng ban.',
            'ma_phong_ban.string' => 'Mã phòng ban phải là chuỗi ký tự.',
            'ma_phong_ban.max' => 'Mã phòng ban không được quá 50 ký tự.',
            'ma_phong_ban.unique' => 'Mã phòng ban đã tồn tại, vui lòng chọn mã khác.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự.',
        ]);

        $phongBan->update([
            'ten_phong_ban' => $request->input('ten_phong_ban'),
            'ma_phong_ban' => $request->input('ma_phong_ban'),
            'mo_ta' => $request->input('mo_ta'),
            'ghi_chu' => $request->input('ghi_chu'),
        ]);

        return redirect()->route('admin.he-thong.phong-ban')->with('success', 'Đã cập nhật phòng ban thành công.');
    }

    public function destroyPhongBan(PhongBan $phongBan)
    {
        $dangCoNhanSu = $phongBan->nhanViens()->exists();
        if ($dangCoNhanSu) {
            return redirect()
                ->route('admin.he-thong.phong-ban')
                ->with('error', 'Đang tồn tại nhân sự trực thuộc phòng ban này. Không thể xoá.');
        }

        $phongBan->delete();

        return redirect()->route('admin.he-thong.phong-ban')->with('success', 'Đã xóa phòng ban.');
    }

    /**
     * Danh sách nhân viên thuộc phòng ban (JSON cho modal).
     */
    public function nhanVienPhongBan(PhongBan $phongBan): JsonResponse
    {
        $items = $phongBan->nhanViens()
            ->with('user')
            ->orderBy('id')
            ->get()
            ->map(static function (NhanVien $nv) {
                $user = $nv->user;

                return [
                    'id' => (int) $nv->id,
                    'hinh_anh' => $nv->hinh_anh ? asset('storage/'.$nv->hinh_anh) : null,
                    'name' => $user?->name ?? '—',
                    'email' => $user?->email ?? '',
                    'phone' => $user?->phone ?? '',
                    'role_label' => $user?->role_label ?? '—',
                    'gioi_tinh' => $nv->gioi_tinh ?? '',
                    'ngay_sinh' => $nv->ngay_sinh?->format('d/m/Y') ?? '',
                    'cccd' => $nv->cccd ?? '',
                    'vi_tri_lam_viec' => $nv->vi_tri_lam_viec ?? '',
                    'ngay_vao_cong_ty' => $nv->ngay_vao_cong_ty?->format('d/m/Y') ?? '',
                    'ngay_ky_hop_dong' => $nv->ngay_ky_hop_dong?->format('d/m/Y') ?? '',
                    'luong_co_ban' => $nv->luong_co_ban !== null ? number_format($nv->luong_co_ban) : '',
                    'luong_tang_ca' => $nv->luong_tang_ca !== null ? number_format($nv->luong_tang_ca) : '',
                    'ngan_hang' => $nv->ngan_hang ?? '',
                    'chi_nhanh' => $nv->chi_nhanh ?? '',
                    'so_tai_khoan' => $nv->so_tai_khoan ?? '',
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'phong_ban' => [
                'id' => (int) $phongBan->id,
                'ten_phong_ban' => $phongBan->ten_phong_ban,
                'ma_phong_ban' => $phongBan->ma_phong_ban,
            ],
            'items' => $items,
            'total' => count($items),
        ]);
    }

    public function taiLieu(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(TaiLieu::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = TaiLieu::query();

        $search = trim((string) ($validated['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_tai_lieu', 'like', $like)
                    ->orWhere('mo_ta', 'like', $like);
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? TaiLieu::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, TaiLieu::SAP_XEP_OPTIONS)) {
            $sapXepTheo = TaiLieu::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            TaiLieu::SAP_XEP_TEN => $query->orderBy('ten_tai_lieu', $thuTu),
            TaiLieu::SAP_XEP_CREATED_AT => $query->orderBy('created_at', $thuTu),
            default => $query->orderBy('created_at', $thuTu),
        };
        $query->orderBy('id', $thuTu);

        $danhSachTaiLieu = $query->paginate(AdminPagination::perPage())->withQueryString();

        return view('admin.he-thong.tai-lieu', compact('danhSachTaiLieu'));
    }

    public function storeTaiLieu(Request $request)
    {
        $request->validate([
            'tap_tin' => 'required|file|max:20480',
            'ten_tai_lieu' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
        ], [
            'tap_tin.required' => 'Vui lòng chọn file tải lên.',
            'tap_tin.file' => 'File tải lên không hợp lệ.',
            'tap_tin.max' => 'Dung lượng file không được vượt quá 20MB.',
            'ten_tai_lieu.required' => 'Vui lòng nhập tên tài liệu.',
            'ten_tai_lieu.string' => 'Tên tài liệu phải là chuỗi ký tự.',
            'ten_tai_lieu.max' => 'Tên tài liệu không được vượt quá 255 ký tự.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
        ]);

        $tapTin = $request->file('tap_tin');
        $duongDanLuuTru = $tapTin->store('taiLieu', 'public');

        try {
            TaiLieu::create([
                'ten_tai_lieu' => $request->input('ten_tai_lieu'),
                'mo_ta' => $request->input('mo_ta'),
                'file' => $tapTin->getClientOriginalName(),
                'duong_dan' => $duongDanLuuTru,
            ]);
        } catch (\Throwable $th) {
            Storage::disk('public')->delete($duongDanLuuTru);

            throw $th;
        }

        return redirect()
            ->route('admin.he-thong.tai-lieu')
            ->with('success', 'Đã thêm tài liệu thành công.');
    }

    public function destroyTaiLieu(TaiLieu $taiLieu)
    {
        if (! empty($taiLieu->duong_dan) && Storage::disk('public')->exists($taiLieu->duong_dan)) {
            Storage::disk('public')->delete($taiLieu->duong_dan);
        }

        $taiLieu->delete();

        return redirect()
            ->route('admin.he-thong.tai-lieu')
            ->with('success', 'Đã xóa tài liệu.');
    }

    public function logs(Request $request)
    {
        $validated = $request->validate([
            'ngay' => 'nullable|date_format:Y-m-d',
            'user_id' => 'nullable|integer|min:1',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|integer|min:100|max:599',
            'method' => 'nullable|string|in:GET,POST,PUT,PATCH,DELETE',
        ]);

        $availableDates = UserActionLogReader::availableDates();
        $ngay = $validated['ngay'] ?? ($availableDates[0] ?? now()->format('Y-m-d'));

        if (! in_array($ngay, $availableDates, true) && $availableDates !== []) {
            $ngay = $availableDates[0];
        }

        $userId = isset($validated['user_id']) ? (int) $validated['user_id'] : null;
        $search = isset($validated['search']) ? trim((string) $validated['search']) : null;
        if ($search === '') {
            $search = null;
        }
        $responseStatus = isset($validated['status']) ? (int) $validated['status'] : null;
        $httpMethod = isset($validated['method']) ? strtoupper((string) $validated['method']) : null;

        $danhSach = UserActionLogReader::paginate(
            $ngay,
            AdminPagination::perPage($request),
            max(1, (int) $request->query('page', 1)),
            $userId,
            $search,
            $responseStatus,
            $httpMethod,
        );

        $coFileLog = UserActionLogReader::resolveLogPath($ngay) !== null;

        return view('admin.he-thong.logs', compact(
            'danhSach',
            'availableDates',
            'ngay',
            'userId',
            'search',
            'responseStatus',
            'httpMethod',
            'coFileLog',
        ));
    }

    public function destroyLogs(Request $request)
    {
        $validated = $request->validate([
            'ngay' => 'required|date_format:Y-m-d',
        ]);

        $ngay = $validated['ngay'];

        if (! UserActionLogReader::deleteLogFile($ngay)) {
            return redirect()
                ->route('admin.he-thong.logs', ['ngay' => $ngay])
                ->with('error', 'Không tìm thấy file log cho ngày đã chọn.');
        }

        $availableDates = UserActionLogReader::availableDates();
        $redirectNgay = $availableDates[0] ?? now()->format('Y-m-d');

        return redirect()
            ->route('admin.he-thong.logs', ['ngay' => $redirectNgay])
            ->with('success', 'Đã xóa file log ngày '.Carbon::parse($ngay)->format('d/m/Y').'.');
    }
}

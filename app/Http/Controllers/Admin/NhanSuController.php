<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concept;
use App\Models\HopDongCuoi;
use App\Models\NhanVien;
use App\Models\PhongBan;
use App\Models\User;
use App\Models\VaiTro;
use App\Support\AdminPagination;
use App\Support\HopDongCuoiLocTienDoFilter;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NhanSuController extends Controller
{
    private function lichLamViecStartAt(HopDongCuoi $hd, string $tz): ?Carbon
    {
        $ngayChup = $hd->ngay_chup_thuc_te ?? $hd->ngay_chup_du_kien;
        if (! $ngayChup) {
            return null;
        }

        $start = Carbon::parse($ngayChup, $tz)->startOfDay();
        $gioChup = trim((string) ($hd->gio_chup ?? ''));

        if ($gioChup !== '' && preg_match('/^(?<hour>\d{1,2}):(?<minute>\d{2})/', $gioChup, $matches)) {
            $start->setTime((int) $matches['hour'], (int) $matches['minute']);
        } else {
            $start->setTime(8, 0);
        }

        return $start;
    }

    private function lichLamViecRoleKeys(HopDongCuoi $hd, ?int $nhanVienId, bool $isAdmin): array
    {
        $assigned = array_filter([
            'chup' => $hd->tho_chup_id ? (int) $hd->tho_chup_id : null,
            'make' => $hd->tho_make_id ? (int) $hd->tho_make_id : null,
            'edit' => $hd->tho_edit_id ? (int) $hd->tho_edit_id : null,
        ]);

        if ($isAdmin || ! $nhanVienId) {
            return array_values(array_keys($assigned));
        }

        $nhanVienId = (int) $nhanVienId;

        return array_values(array_keys(array_filter($assigned, fn ($assignedId) => (int) $assignedId === $nhanVienId)));
    }

    private function lichLamViecRoleLabels(array $roleKeys): array
    {
        $map = [
            'chup' => 'Chụp',
            'make' => 'Make',
            'edit' => 'Edit',
        ];

        return array_values(array_map(
            fn ($key) => $map[$key] ?? ucfirst((string) $key),
            array_values(array_filter($roleKeys, fn ($key) => isset($map[$key])))
        ));
    }

    private function lichLamViecTenGoiChup(HopDongCuoi $hd): ?string
    {
        $tenDichVu = $hd->nhomDichVu?->ten_nhom
            ?? $hd->concept?->ten_concept
            ?? null;

        if ($tenDichVu === null && $hd->loai_dich_vu) {
            $tenDichVu = (string) str($hd->loai_dich_vu)->replace('_', ' ');
        }

        $tenDichVu = trim((string) ($tenDichVu ?? ''));

        return $tenDichVu !== '' ? $tenDichVu : null;
    }

    private function lichLamViecGhiChu(HopDongCuoi $hd): ?string
    {
        $ghiChu = trim((string) ($hd->yeu_cau_dac_biet ?: ($hd->ghi_chu_sale ?? '')));

        return $ghiChu !== '' ? $ghiChu : null;
    }

    private function lichLamViecCapDoi(HopDongCuoi $hd): string
    {
        $tenChuRe = trim((string) ($hd->ten_chu_re ?? ''));
        $tenCoDau = trim((string) ($hd->ten_co_dau ?? ''));
        $capDoi = trim($tenChuRe.($tenChuRe !== '' && $tenCoDau !== '' ? ' - ' : '').$tenCoDau);

        if ($capDoi !== '') {
            return $capDoi;
        }

        $maHopDong = trim((string) ($hd->ma_hop_dong ?? ''));

        return $maHopDong !== '' ? $maHopDong : ('HĐ #'.$hd->id);
    }

    private function lichLamViecCapDoiRutGon(HopDongCuoi $hd): string
    {
        $rutGonChuRe = \App\Support\LichLamViecTenRutGon::hoTen($hd->ten_chu_re);
        $rutGonCoDau = \App\Support\LichLamViecTenRutGon::hoTen($hd->ten_co_dau);

        if ($rutGonChuRe !== '' && $rutGonCoDau !== '') {
            return $rutGonChuRe.' - '.$rutGonCoDau;
        }

        if ($rutGonChuRe !== '') {
            return $rutGonChuRe;
        }

        if ($rutGonCoDau !== '') {
            return $rutGonCoDau;
        }

        return $this->lichLamViecCapDoi($hd);
    }

    private function lichLamViecTienDo(HopDongCuoi $hd): string
    {
        if (HopDongCuoiLocTienDoFilter::matchesChuaPhanCong($hd)) {
            return 'chua_phan_cong';
        }

        if ($hd->ngay_up_link_in_gan_nhat || trim((string) ($hd->link_in ?? '')) !== '') {
            return 'up_link_in';
        }
        if ($hd->ngay_up_link_demo_gan_nhat || trim((string) ($hd->link_demo ?? '')) !== '') {
            return 'up_link_demo';
        }
        if ($hd->tho_edit_id) {
            return 'phan_edit';
        }
        if ($hd->tho_make_id) {
            return 'phan_make';
        }
        if ($hd->tho_chup_id) {
            return 'phan_chup';
        }

        return 'mac_dinh';
    }

    private function lichLamViecTienDoLabel(string $tienDo): ?string
    {
        $cfg = config('lich_lam_viec.tien_do.'.$tienDo);

        return is_array($cfg) && ! empty($cfg['label'])
            ? (string) $cfg['label']
            : null;
    }

    private function lichLamViecTrangThaiHopDongLabel(?string $trangThai): ?string
    {
        if (! $trangThai) {
            return null;
        }

        $map = [
            'nhap' => 'Nháp',
            'da_huy' => 'Đã huỷ',
            'dang_thuc_hien' => 'Đang thực hiện',
            'tre_chup' => 'Trễ chụp',
            'tre_edit' => 'Trễ edit',
        ];

        return $map[$trangThai] ?? $trangThai;
    }

    private function lichLamViecTyLeThanhToan(HopDongCuoi $hd): int
    {
        $tongTien = $hd->tongPhaiThu();
        if ($tongTien <= 0) {
            return 0;
        }

        return (int) min(100, (int) round($hd->tongDaThanhToan() / $tongTien * 100));
    }

    private function lichLamViecExcludeNhap($query): void
    {
        $query->whereNotIn('trang_thai_hop_dong', ['nhap']);
    }

    private function lichLamViecNgayLabel(string $dateStr, string $tz): string
    {
        $carbon = Carbon::parse($dateStr, $tz)->startOfDay();
        $labels = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];

        return ($labels[$carbon->dayOfWeek] ?? '').', '.$carbon->format('d/m/Y');
    }

    private function lichLamViecBriefKhachHang(HopDongCuoi $hd): string
    {
        $ten = collect([$hd->ten_chu_re ?? '', $hd->ten_co_dau ?? ''])
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->implode(' / ');

        return $ten !== '' ? $ten : '—';
    }

    private function lichLamViecBriefSdt(HopDongCuoi $hd): string
    {
        $sdt = trim((string) ($hd->email_sdt_chu_re ?? ''));
        if ($sdt === '') {
            $sdt = trim((string) ($hd->email_sdt_co_dau ?? ''));
        }

        return $sdt !== '' ? $sdt : '—';
    }

    private function lichLamViecBriefNgayChup(HopDongCuoi $hd, string $tz): string
    {
        $ngayChup = $hd->ngay_chup_thuc_te ?? $hd->ngay_chup_du_kien;
        if (! $ngayChup) {
            return '—';
        }

        $str = Carbon::parse($ngayChup, $tz)->format('d/m/Y');
        $buoiLabels = ['sang' => 'Sáng', 'chieu' => 'Chiều', 'ca_ngay' => 'Cả ngày'];
        $buoiKey = (string) ($hd->buoi_chup ?? '');
        if ($buoiKey !== '' && isset($buoiLabels[$buoiKey])) {
            $str .= ' · '.$buoiLabels[$buoiKey];
        }

        return $str;
    }

    private function lichLamViecBriefConcept(HopDongCuoi $hd): string
    {
        $ten = trim((string) ($hd->concept?->ten_concept ?? ''));

        return $ten !== '' ? $ten : '—';
    }

    private function lichLamViecBriefTrangPhuc(HopDongCuoi $hd): string
    {
        $names = collect($hd->hopDongCuoiTrangPhuc ?? [])
            ->map(fn ($row) => trim((string) ($row->trangPhuc?->ten_san_pham ?? '')))
            ->filter()
            ->values();

        return $names->isNotEmpty() ? $names->implode(', ') : '—';
    }

    /** @return array<string, string> */
    private function lichLamViecBriefFields(HopDongCuoi $hd, string $tz): array
    {
        $nguoiChup = trim((string) ($hd->thoChup?->user?->name ?? ''));
        $diaDiem = trim((string) ($hd->dia_diem_chup ?? ''));
        $ghiChu = $this->lichLamViecGhiChu($hd);

        return [
            'khach_hang' => $this->lichLamViecBriefKhachHang($hd),
            'sdt' => $this->lichLamViecBriefSdt($hd),
            'ngay_chup' => $this->lichLamViecBriefNgayChup($hd, $tz),
            'nguoi_chup' => $nguoiChup !== '' ? $nguoiChup : '—',
            'dia_diem' => $diaDiem !== '' ? $diaDiem : '—',
            'concept' => $this->lichLamViecBriefConcept($hd),
            'trang_phuc' => $this->lichLamViecBriefTrangPhuc($hd),
            'ghi_chu' => $ghiChu !== null && $ghiChu !== '' ? $ghiChu : '—',
        ];
    }

    private function lichLamViecHopDongSummary(HopDongCuoi $hd, ?int $nhanVienId, bool $isAdmin, string $tz): array
    {
        $start = $this->lichLamViecStartAt($hd, $tz);
        $roleKeys = $this->lichLamViecRoleKeys($hd, $nhanVienId, $isAdmin);
        $tienDo = $this->lichLamViecTienDo($hd);

        return [
            'id' => (int) $hd->id,
            'trang_thai_hop_dong' => $hd->trang_thai_hop_dong ? (string) $hd->trang_thai_hop_dong : null,
            'trang_thai_hop_dong_label' => $this->lichLamViecTrangThaiHopDongLabel($hd->trang_thai_hop_dong),
            'ma_hop_dong' => $hd->ma_hop_dong ? (string) $hd->ma_hop_dong : null,
            'time' => $start ? $start->format('H:i') : null,
            'datetime' => $start ? $start->toIso8601String() : null,
            'couple' => $this->lichLamViecCapDoi($hd),
            'couple_short' => $this->lichLamViecCapDoiRutGon($hd),
            'goi_chup' => $this->lichLamViecTenGoiChup($hd),
            'dia_diem' => $hd->dia_diem_chup ? trim((string) $hd->dia_diem_chup) : null,
            'ghi_chu' => $this->lichLamViecGhiChu($hd),
            'roles' => $roleKeys,
            'role_labels' => $this->lichLamViecRoleLabels($roleKeys),
            'phan_cong' => [
                'chup' => $hd->thoChup?->user?->name,
                'make' => $hd->thoMake?->user?->name,
                'edit' => $hd->thoEdit?->user?->name,
            ],
            'ngay_up_link_demo_gan_nhat' => $hd->ngay_up_link_demo_gan_nhat
                ? $hd->ngay_up_link_demo_gan_nhat->copy()->timezone($tz)->format('d/m/Y H:i')
                : null,
            'ngay_up_link_in_gan_nhat' => $hd->ngay_up_link_in_gan_nhat
                ? $hd->ngay_up_link_in_gan_nhat->copy()->timezone($tz)->format('d/m/Y H:i')
                : null,
            'tien_do' => $tienDo,
            'tien_do_label' => $this->lichLamViecTienDoLabel($tienDo),
            'ty_le_thanh_toan' => $this->lichLamViecTyLeThanhToan($hd),
            'brief' => $this->lichLamViecBriefFields($hd, $tz),
        ];
    }

    /** @return \Illuminate\Support\Collection<int, array<string, mixed>> */
    private function lichLamViecLayHopDongChuaPhanCong(?int $nhanVienId, bool $isAdmin, string $tz)
    {
        if (! $isAdmin) {
            return collect();
        }

        return HopDongCuoi::query()
            ->with(['concept', 'nhomDichVu', 'thoChup.user', 'thoMake.user', 'thoEdit.user'])
            ->tap(fn ($q) => $this->lichLamViecExcludeNhap($q))
            ->whereNotIn('trang_thai_hop_dong', ['da_huy'])
            ->tap(fn ($q) => HopDongCuoiLocTienDoFilter::applyChuaPhanCong($q))
            ->whereNull('ngay_chup_thuc_te')
            ->whereNull('ngay_chup_du_kien')
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(fn (HopDongCuoi $hd) => $this->lichLamViecHopDongSummary($hd, $nhanVienId, $isAdmin, $tz));
    }

    public function lichLamViecChuaPhanCong(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user?->isAdmin() ?? false;
        $nhanVienId = $user?->nhanVien?->id;
        if (! $isAdmin && ! $nhanVienId) {
            return response()->json(['items' => []]);
        }

        $tz = config('app.timezone');
        $items = $this->lichLamViecLayHopDongChuaPhanCong($nhanVienId, $isAdmin, $tz)->values();

        return response()->json(['items' => $items]);
    }

    public function index()
    {
        return view('admin.nhan-su.index');
    }

    public function danhSach(Request $request)
    {
        $validated = $request->validate([
            'tu_khoa' => 'nullable|string|max:200',
            'gioi_tinh' => 'nullable|string|in:'.implode(',', array_keys(User::GIOI_TINH_OPTIONS)),
            'phong_ban_id' => 'nullable|integer|exists:phong_ban,id',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(User::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = User::query()
            ->where('role', '!=', '99')
            ->with(['nhanVien', 'nhanVien.phongBan', 'vaiTro']);

        $tuKhoa = trim((string) ($validated['tu_khoa'] ?? ''));
        if ($tuKhoa !== '') {
            $like = '%'.addcslashes($tuKhoa, '%_\\').'%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhereHas('nhanVien', function ($nv) use ($like) {
                        $nv->where('cccd', 'like', $like)
                            ->orWhere('vi_tri_lam_viec', 'like', $like);
                    });
            });
        }

        $gioiTinh = trim((string) ($validated['gioi_tinh'] ?? ''));
        if ($gioiTinh !== '') {
            $query->whereHas('nhanVien', fn ($nv) => $nv->where('gioi_tinh', $gioiTinh));
        }

        $phongBanId = $validated['phong_ban_id'] ?? null;
        if ($phongBanId !== null) {
            $query->whereHas('nhanVien.phongBan', fn ($pb) => $pb->whereKey((int) $phongBanId));
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? User::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, User::SAP_XEP_OPTIONS)) {
            $sapXepTheo = User::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';

        $sortNhanVienColumns = [
            User::SAP_XEP_NGAY_SINH => 'ngay_sinh',
            User::SAP_XEP_NGAY_VAO_CONG_TY => 'ngay_vao_cong_ty',
            User::SAP_XEP_NGAY_KY_HOP_DONG => 'ngay_ky_hop_dong',
            User::SAP_XEP_LUONG_CO_BAN => 'luong_co_ban',
            User::SAP_XEP_LUONG_TANG_CA => 'luong_tang_ca',
        ];

        if (array_key_exists($sapXepTheo, $sortNhanVienColumns)) {
            $query->leftJoin('nhan_vien as nv_sort', 'users.id', '=', 'nv_sort.user_id')
                ->select('users.*')
                ->orderBy('nv_sort.'.$sortNhanVienColumns[$sapXepTheo], $thuTu);
        } elseif ($sapXepTheo === User::SAP_XEP_HO_TEN) {
            $query->orderBy('name', $thuTu);
        } elseif ($sapXepTheo === User::SAP_XEP_VAI_TRO) {
            $query->orderBy('role', $thuTu);
        } else {
            $query->orderBy('id', $thuTu);
        }

        $danhSach = $query->paginate(AdminPagination::perPage())->withQueryString();

        $phongBans = PhongBan::orderBy('ten_phong_ban')->get();
        $dsVaiTro = VaiTro::query()->orderBy('ma_vai_tro')->get(['id', 'ma_vai_tro', 'ten_vai_tro']);
        $maVaiTroMacDinh = VaiTro::maMacDinhNhanVien();

        return view('admin.nhan-su.danh-sach', compact('danhSach', 'phongBans', 'dsVaiTro', 'maVaiTroMacDinh'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')],
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password',
            'gioi_tinh' => 'nullable|string|in:nam,nu,khac',
            'ngay_sinh' => 'nullable|date',
            'cccd' => ['nullable', 'string', 'max:20', Rule::unique('nhan_vien', 'cccd')],
            'role' => VaiTro::quyTacValidateRole(),
            'vi_tri_lam_viec' => 'nullable|string|max:255',
            'ngay_vao_cong_ty' => 'nullable|date',
            'ngay_ky_hop_dong' => 'nullable|date',
            'luong_co_ban' => 'nullable|integer|min:0',
            'luong_tang_ca' => 'nullable|integer|min:0',
            'phong_ban_id' => 'required|exists:phong_ban,id',
            'hinh_anh' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.string' => 'Họ tên phải là chuỗi ký tự.',
            'name.max' => 'Họ tên không được quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.max' => 'Số điện thoại không được quá 20 ký tự.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password_confirmation.required_with' => 'Vui lòng xác nhận lại mật khẩu.',
            'gioi_tinh.string' => 'Giới tính phải là chuỗi ký tự.',
            'gioi_tinh.in' => 'Giới tính không hợp lệ.',
            'ngay_sinh.date' => 'Ngày sinh không đúng định dạng.',
            'cccd.string' => 'Số CCCD phải là chuỗi ký tự.',
            'cccd.max' => 'Số CCCD không được quá 20 ký tự.',
            'cccd.unique' => 'Số CCCD này đã được sử dụng.',
            'role.exists' => 'Vai trò không hợp lệ.',
            'vi_tri_lam_viec.string' => 'Vị trí làm việc phải là chuỗi ký tự.',
            'vi_tri_lam_viec.max' => 'Vị trí làm việc không được quá 255 ký tự.',
            'ngay_vao_cong_ty.date' => 'Ngày vào công ty không đúng định dạng.',
            'ngay_ky_hop_dong.date' => 'Ngày ký hợp đồng không đúng định dạng.',
            'luong_co_ban.integer' => 'Lương cơ bản phải là số nguyên.',
            'luong_co_ban.min' => 'Lương cơ bản không được âm.',
            'luong_tang_ca.integer' => 'Lương tăng ca phải là số nguyên.',
            'luong_tang_ca.min' => 'Lương tăng ca không được âm.',
            'phong_ban_id.required' => 'Vui lòng chọn phòng ban.',
            'phong_ban_id.exists' => 'Phòng ban không tồn tại.',
            'hinh_anh.image' => 'File tải lên phải là ảnh (jpeg, png, bmp, gif, webp).',
            'hinh_anh.max' => 'Kích thước ảnh không được quá 2MB.',
        ]);

        $validated['password'] = Hash::make($request->password);
        $validated['role'] = (int) $request->input('role', VaiTro::maMacDinhNhanVien());

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $request->input('phone'),
                'password' => $validated['password'],
                'role' => $validated['role'],
            ]);

            $hinhAnhPath = null;
            if ($request->hasFile('hinh_anh')) {
                $hinhAnhPath = $request->file('hinh_anh')->store('nhan-vien', 'public');
            }

            $nhanVien = NhanVien::create([
                'user_id' => $user->id,
                'hinh_anh' => $hinhAnhPath,
                'phong_ban' => PhongBan::maFromId((int) $request->input('phong_ban_id')),
                'gioi_tinh' => $request->input('gioi_tinh'),
                'ngay_sinh' => $request->input('ngay_sinh'),
                'cccd' => $request->input('cccd'),
                'vi_tri_lam_viec' => $request->input('vi_tri_lam_viec'),
                'ngay_vao_cong_ty' => $request->input('ngay_vao_cong_ty'),
                'ngay_ky_hop_dong' => $request->input('ngay_ky_hop_dong'),
                'luong_co_ban' => $request->input('luong_co_ban', 50000),
                'luong_tang_ca' => $request->input('luong_tang_ca', 80000),
            ]);

            DB::commit();

            return redirect()->route('admin.nhan-su.danh-sach')->with('success', 'Đã thêm nhân sự mới thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'gioi_tinh' => 'nullable|string|in:nam,nu,khac',
            'ngay_sinh' => 'nullable|date',
            'cccd' => ['nullable', 'string', 'max:20'],
            'role' => VaiTro::quyTacValidateRole(),
            'vi_tri_lam_viec' => 'nullable|string|max:255',
            'ngay_vao_cong_ty' => 'nullable|date',
            'ngay_ky_hop_dong' => 'nullable|date',
            'luong_co_ban' => 'nullable|integer|min:0',
            'luong_tang_ca' => 'nullable|integer|min:0',
            'phong_ban_id' => 'required|exists:phong_ban,id',
            'hinh_anh' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.string' => 'Họ tên phải là chuỗi ký tự.',
            'name.max' => 'Họ tên không được quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.max' => 'Số điện thoại không được quá 20 ký tự.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'gioi_tinh.string' => 'Giới tính phải là chuỗi ký tự.',
            'gioi_tinh.in' => 'Giới tính không hợp lệ.',
            'ngay_sinh.date' => 'Ngày sinh không đúng định dạng.',
            'cccd.string' => 'Số CCCD phải là chuỗi ký tự.',
            'cccd.max' => 'Số CCCD không được quá 20 ký tự.',
            'role.exists' => 'Vai trò không hợp lệ.',
            'vi_tri_lam_viec.string' => 'Vị trí làm việc phải là chuỗi ký tự.',
            'vi_tri_lam_viec.max' => 'Vị trí làm việc không được quá 255 ký tự.',
            'ngay_vao_cong_ty.date' => 'Ngày vào công ty không đúng định dạng.',
            'ngay_ky_hop_dong.date' => 'Ngày ký hợp đồng không đúng định dạng.',
            'luong_co_ban.integer' => 'Lương cơ bản phải là số nguyên.',
            'luong_co_ban.min' => 'Lương cơ bản không được âm.',
            'luong_tang_ca.integer' => 'Lương tăng ca phải là số nguyên.',
            'luong_tang_ca.min' => 'Lương tăng ca không được âm.',
            'phong_ban_id.required' => 'Vui lòng chọn phòng ban.',
            'phong_ban_id.exists' => 'Phòng ban không tồn tại.',
            'hinh_anh.image' => 'File tải lên phải là ảnh (jpeg, png, bmp, gif, webp).',
            'hinh_anh.max' => 'Kích thước ảnh không được quá 2MB.',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $request->input('phone'),
                'role' => (int) $request->input('role', $user->role),
            ]);

            $nhanVien = $user->nhanVien;
            $hinhAnhPath = $nhanVien?->hinh_anh;

            if ($request->hasFile('hinh_anh')) {
                if ($hinhAnhPath) {
                    Storage::disk('public')->delete($hinhAnhPath);
                }
                $hinhAnhPath = $request->file('hinh_anh')->store('nhan-vien', 'public');
            }

            if ($nhanVien) {
                $nhanVien->update([
                    'hinh_anh' => $hinhAnhPath,
                    'phong_ban' => PhongBan::maFromId((int) $request->input('phong_ban_id')),
                    'gioi_tinh' => $request->input('gioi_tinh'),
                    'ngay_sinh' => $request->input('ngay_sinh'),
                    'cccd' => $request->input('cccd'),
                    'vi_tri_lam_viec' => $request->input('vi_tri_lam_viec'),
                    'ngay_vao_cong_ty' => $request->input('ngay_vao_cong_ty'),
                    'ngay_ky_hop_dong' => $request->input('ngay_ky_hop_dong'),
                    'luong_co_ban' => $request->filled('luong_co_ban') ? (int) $request->input('luong_co_ban') : $nhanVien->luong_co_ban,
                    'luong_tang_ca' => $request->filled('luong_tang_ca') ? (int) $request->input('luong_tang_ca') : $nhanVien->luong_tang_ca,
                ]);
            } else {
                $nhanVien = NhanVien::create([
                    'user_id' => $user->id,
                    'hinh_anh' => $hinhAnhPath,
                    'phong_ban' => PhongBan::maFromId((int) $request->input('phong_ban_id')),
                    'gioi_tinh' => $request->input('gioi_tinh'),
                    'ngay_sinh' => $request->input('ngay_sinh'),
                    'cccd' => $request->input('cccd'),
                    'vi_tri_lam_viec' => $request->input('vi_tri_lam_viec'),
                    'ngay_vao_cong_ty' => $request->input('ngay_vao_cong_ty'),
                    'ngay_ky_hop_dong' => $request->input('ngay_ky_hop_dong'),
                    'luong_co_ban' => $request->input('luong_co_ban', 50000),
                    'luong_tang_ca' => $request->input('luong_tang_ca', 80000),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.nhan-su.danh-sach')->with('success', 'Đã cập nhật nhân sự thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    public function doiMatKhau(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password_confirmation.required_with' => 'Vui lòng xác nhận lại mật khẩu.',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('admin.nhan-su.danh-sach')->with('success', 'Đã đổi mật khẩu thành công.');
    }

    public function destroy(User $user)
    {
        DB::beginTransaction();
        try {
            $nhanVien = $user->nhanVien;
            if ($nhanVien?->hinh_anh) {
                Storage::disk('public')->delete($nhanVien->hinh_anh);
            }
            if ($nhanVien) {
                $nhanVien->delete();
            }
            $user->delete();
            DB::commit();

            return redirect()->route('admin.nhan-su.danh-sach')->with('success', 'Đã xóa nhân sự thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    public function lichLamViec()
    {
        $user = auth()->user();
        $isAdmin = $user?->isAdmin() ?? false;
        $nhanVienId = $user?->nhanVien?->id;
        $ngayChupExpr = 'COALESCE(ngay_chup_thuc_te, ngay_chup_du_kien)';

        $homNay = Carbon::now(config('app.timezone'));
        $batDauTuan = $homNay->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $ketThucTuan = $homNay->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $dsNgayTrongTuan = collect(
            CarbonPeriod::create($batDauTuan->copy()->startOfDay(), '1 day', $ketThucTuan->copy()->startOfDay())
        );

        $hopDongTrongTuan = collect();
        if ($isAdmin || $nhanVienId) {
            $hopDongTrongTuan = HopDongCuoi::query()
                ->with(['concept', 'thoChup', 'thoMake', 'thoEdit'])
                ->tap(fn ($q) => $this->lichLamViecExcludeNhap($q))
                ->whereNotNull(DB::raw($ngayChupExpr))
                ->whereBetween(DB::raw("DATE($ngayChupExpr)"), [$batDauTuan->toDateString(), $ketThucTuan->toDateString()])
                ->when(! $isAdmin, function ($q) use ($nhanVienId) {
                    $q->where(function ($qq) use ($nhanVienId) {
                        $qq->where('tho_chup_id', $nhanVienId)
                            ->orWhere('tho_make_id', $nhanVienId)
                            ->orWhere('tho_edit_id', $nhanVienId);
                    });
                })
                ->orderByRaw("DATE($ngayChupExpr)")
                ->get();
        }

        $danhSachNhanVienEdit = NhanVien::query()
            ->with('user')
            ->thuocMaPhongBan(PhongBan::MA_EDIT)
            ->orderBy('id')
            ->get();

        $tienDoLegend = config('lich_lam_viec.tien_do', []);
        $locTienDoFilters = config('lich_lam_viec.loc_tien_do', []);

        return view('admin.nhan-su.lich-lam-viec', compact(
            'batDauTuan',
            'ketThucTuan',
            'dsNgayTrongTuan',
            'hopDongTrongTuan',
            'danhSachNhanVienEdit',
            'nhanVienId',
            'isAdmin',
            'tienDoLegend',
            'locTienDoFilters'
        ));
    }

    public function lichLamViecData(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user?->isAdmin() ?? false;
        $nhanVienId = $user?->nhanVien?->id;
        $ngayChupExpr = 'COALESCE(ngay_chup_thuc_te, ngay_chup_du_kien)';
        if (! $isAdmin && ! $nhanVienId) {
            return response()->json([]);
        }

        $start = $request->query('start');
        $end = $request->query('end');
        if (! $start || ! $end) {
            return response()->json([]);
        }

        $tz = config('app.timezone');
        $startDt = Carbon::parse($start, $tz);
        $endDtExclusive = Carbon::parse($end, $tz); // FullCalendar end là mốc exclusive
        $locFilters = HopDongCuoiLocTienDoFilter::parseFromRequest($request);

        // View tháng / danh sách: gom theo ngày
        $startDate = $startDt->toDateString();
        $endDate = $endDtExclusive->copy()->subDay()->toDateString();

        $hopDongs = HopDongCuoi::query()
            ->with(['concept', 'nhomDichVu', 'thoChup.user', 'thoMake.user', 'thoEdit.user'])
            ->tap(fn ($q) => $this->lichLamViecExcludeNhap($q))
            ->whereNotNull(DB::raw($ngayChupExpr))
            ->whereBetween(DB::raw("DATE($ngayChupExpr)"), [$startDate, $endDate])
            ->tap(fn ($q) => HopDongCuoiLocTienDoFilter::apply($q, $locFilters))
            ->when(! $isAdmin, function ($q) use ($nhanVienId) {
                $q->where(function ($qq) use ($nhanVienId) {
                    $qq->where('tho_chup_id', $nhanVienId)
                        ->orWhere('tho_make_id', $nhanVienId)
                        ->orWhere('tho_edit_id', $nhanVienId);
                });
            })
            ->orderByRaw("DATE($ngayChupExpr)")
            ->orderBy('gio_chup')
            ->orderBy('id')
            ->get();

        $grouped = $hopDongs->groupBy(function (HopDongCuoi $hd) use ($tz) {
            $ngayChup = $hd->ngay_chup_thuc_te ?? $hd->ngay_chup_du_kien;
            if (! $ngayChup) {
                return null;
            }

            return Carbon::parse($ngayChup, $tz)->toDateString();
        })->filter(fn ($_, $key) => $key !== null && $key !== '');

        $events = $grouped->map(function ($group, $ngay) use ($nhanVienId, $isAdmin, $tz) {
            $contracts = $group
                ->reject(fn (HopDongCuoi $hd) => ($hd->trang_thai_hop_dong ?? '') === 'nhap')
                ->map(fn (HopDongCuoi $hd) => $this->lichLamViecHopDongSummary($hd, $nhanVienId, $isAdmin, $tz))
                ->values()
                ->all();

            if ($contracts === []) {
                return null;
            }

            return [
                'id' => 'work-'.$ngay,
                'start' => $ngay,
                'allDay' => true,
                'display' => 'block',
                'title' => '',
                'extendedProps' => [
                    'contracts' => $contracts,
                    'has_work' => true,
                ],
            ];
        })->filter()->values();

        return response()->json($events);
    }

    public function lichLamViecDanhSach(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user?->isAdmin() ?? false;
        $nhanVienId = $user?->nhanVien?->id;
        $ngayChupExpr = 'COALESCE(ngay_chup_thuc_te, ngay_chup_du_kien)';

        if (! $isAdmin && ! $nhanVienId) {
            return response()->json([
                'items' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 20,
                    'total' => 0,
                    'last_page' => 1,
                ],
            ]);
        }

        $start = $request->query('start');
        $end = $request->query('end');
        if (! $start || ! $end) {
            return response()->json([
                'items' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 20,
                    'total' => 0,
                    'last_page' => 1,
                ],
            ]);
        }

        $page = max(1, (int) $request->query('page', 1));
        $perPage = (int) $request->query('per_page', 20);
        if (! in_array($perPage, [10, 20, 50], true)) {
            $perPage = 20;
        }

        $tz = config('app.timezone');
        $startDt = Carbon::parse($start, $tz);
        $endDtExclusive = Carbon::parse($end, $tz);
        $startDate = $startDt->toDateString();
        $endDate = $endDtExclusive->copy()->subDay()->toDateString();
        $locFilters = HopDongCuoiLocTienDoFilter::parseFromRequest($request);

        $hopDongs = HopDongCuoi::query()
            ->with(['concept', 'nhomDichVu', 'thoChup.user', 'thoMake.user', 'thoEdit.user'])
            ->tap(fn ($q) => $this->lichLamViecExcludeNhap($q))
            ->whereNotNull(DB::raw($ngayChupExpr))
            ->whereBetween(DB::raw("DATE($ngayChupExpr)"), [$startDate, $endDate])
            ->tap(fn ($q) => HopDongCuoiLocTienDoFilter::apply($q, $locFilters))
            ->when(! $isAdmin, function ($q) use ($nhanVienId) {
                $q->where(function ($qq) use ($nhanVienId) {
                    $qq->where('tho_chup_id', $nhanVienId)
                        ->orWhere('tho_make_id', $nhanVienId)
                        ->orWhere('tho_edit_id', $nhanVienId);
                });
            })
            ->orderByRaw("DATE($ngayChupExpr)")
            ->orderBy('gio_chup')
            ->orderBy('id')
            ->get();

        $rows = $hopDongs
            ->reject(fn (HopDongCuoi $hd) => ($hd->trang_thai_hop_dong ?? '') === 'nhap')
            ->map(function (HopDongCuoi $hd) use ($nhanVienId, $isAdmin, $tz) {
                $ngayChup = $hd->ngay_chup_thuc_te ?? $hd->ngay_chup_du_kien;
                if (! $ngayChup) {
                    return null;
                }
                $ngay = Carbon::parse($ngayChup, $tz)->toDateString();
                $summary = $this->lichLamViecHopDongSummary($hd, $nhanVienId, $isAdmin, $tz);

                return array_merge($summary, [
                    'ngay' => $ngay,
                    'ngay_label' => $this->lichLamViecNgayLabel($ngay, $tz),
                ]);
            })
            ->filter()
            ->values();

        $chuaPhanCongItems = collect();
        if (HopDongCuoiLocTienDoFilter::hasChuaPhanCong($locFilters)) {
            $chuaPhanCongItems = $this->lichLamViecLayHopDongChuaPhanCong($nhanVienId, $isAdmin, $tz);
        }

        $total = $rows->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $items = $rows->forPage($page, $perPage)->values();

        return response()->json([
            'chua_phan_cong' => $chuaPhanCongItems->values()->all(),
            'items' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $total > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => $total > 0 ? min($page * $perPage, $total) : 0,
            ],
            'range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        ]);
    }

    public function lichLamViecHopDongChuaPhanNgay(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user?->isAdmin() ?? false;
        if (! $isAdmin) {
            abort(403);
        }

        $items = HopDongCuoi::query()
            ->whereNotIn('trang_thai_hop_dong', ['nhap', 'da_huy'])
            ->tap(fn ($q) => HopDongCuoiLocTienDoFilter::applyChuaPhanCong($q))
            ->whereNull('ngay_chup_thuc_te')
            ->whereNull('ngay_chup_du_kien')
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(function (HopDongCuoi $hd) {
                return [
                    'id' => (int) $hd->id,
                    'ma_hop_dong' => $hd->ma_hop_dong ? (string) $hd->ma_hop_dong : null,
                    'ten_co_dau' => $hd->ten_co_dau ? (string) $hd->ten_co_dau : null,
                    'ten_chu_re' => $hd->ten_chu_re ? (string) $hd->ten_chu_re : null,
                ];
            })
            ->values();

        return response()->json(['items' => $items]);
    }

    public function lichLamViecHopDongDieuPhoiData(Request $request, HopDongCuoi $hopDongCuoi)
    {
        $user = $request->user();
        $isAdmin = $user?->isAdmin() ?? false;
        if (! $isAdmin) {
            abort(403);
        }

        if (in_array($hopDongCuoi->trang_thai_hop_dong ?? '', ['nhap', 'da_huy'], true)) {
            return response()->json(['message' => 'Hợp đồng nháp hoặc đã huỷ nên không thể phân lịch.'], 422);
        }
        $capNhat = $request->boolean('cap_nhat');
        if (! $capNhat && ($hopDongCuoi->ngay_chup_thuc_te || $hopDongCuoi->ngay_chup_du_kien)) {
            return response()->json(['message' => 'Hợp đồng đã được phân ngày chụp trước đó.'], 422);
        }

        $gioChup = $hopDongCuoi->gio_chup ? substr((string) $hopDongCuoi->gio_chup, 0, 5) : null;

        return response()->json([
            'id' => (int) $hopDongCuoi->id,
            'ma_hop_dong' => $hopDongCuoi->ma_hop_dong ? (string) $hopDongCuoi->ma_hop_dong : null,
            'ten_co_dau' => $hopDongCuoi->ten_co_dau ? (string) $hopDongCuoi->ten_co_dau : null,
            'ten_chu_re' => $hopDongCuoi->ten_chu_re ? (string) $hopDongCuoi->ten_chu_re : null,
            'ngay_chup_thuc_te' => $hopDongCuoi->ngay_chup_thuc_te?->format('Y-m-d'),
            'gio_chup' => $gioChup,
            'ngay_cuoi_chinh_thuc' => $hopDongCuoi->ngay_cuoi_chinh_thuc?->format('Y-m-d'),
            'dia_diem_chup' => $hopDongCuoi->dia_diem_chup ? (string) $hopDongCuoi->dia_diem_chup : '',
            'ngay_tra_link_demo_chinh_thuc' => $hopDongCuoi->ngay_tra_link_demo_chinh_thuc?->format('Y-m-d'),
            'ngay_tra_link_in_chinh_thuc' => $hopDongCuoi->ngay_tra_link_in_chinh_thuc?->format('Y-m-d'),
            'tho_chup_id' => $hopDongCuoi->tho_chup_id,
            'tho_make_id' => $hopDongCuoi->tho_make_id,
            'tho_edit_id' => $hopDongCuoi->tho_edit_id,
            'ghi_chu_sale' => $hopDongCuoi->ghi_chu_sale ? (string) $hopDongCuoi->ghi_chu_sale : '',
        ]);
    }

    /**
     * @return array<int, true>
     */
    private function layTapIdNhanVienBanChoNgayChupThucTe(string $ngayYmd, int $truHopDongCuoiId): array
    {
        $rows = HopDongCuoi::query()
            ->where('id', '!=', $truHopDongCuoiId)
            ->whereDate('ngay_chup_thuc_te', $ngayYmd)
            ->where(function ($q) {
                $q->whereNotNull('tho_chup_id')->orWhereNotNull('tho_make_id');
            })
            ->get(['tho_chup_id', 'tho_make_id']);

        $out = [];
        foreach ($rows as $r) {
            if ($r->tho_chup_id) {
                $out[(int) $r->tho_chup_id] = true;
            }
            if ($r->tho_make_id) {
                $out[(int) $r->tho_make_id] = true;
            }
        }

        return $out;
    }

    public function lichLamViecTaoLich(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user?->isAdmin() ?? false;
        if (! $isAdmin) {
            abort(403);
        }

        $validated = $request->validate([
            'hop_dong_id' => ['required', 'integer', 'exists:hop_dong_cuoi,id'],
            'ngay_chup_thuc_te' => ['required', 'date'],
            'gio_chup' => ['nullable', 'date_format:H:i'],
            'ngay_cuoi_chinh_thuc' => ['nullable', 'date'],
            'dia_diem_chup' => ['nullable', 'string'],
            'ngay_tra_link_demo_chinh_thuc' => ['nullable', 'date'],
            'ngay_tra_link_in_chinh_thuc' => ['nullable', 'date'],
            'tho_chup_id' => ['nullable', 'integer', 'exists:nhan_vien,id'],
            'tho_make_id' => ['nullable', 'integer', 'exists:nhan_vien,id'],
            'tho_edit_id' => ['nullable', 'integer', 'exists:nhan_vien,id'],
            'ghi_chu_sale' => ['nullable', 'string'],
        ], [
            'hop_dong_id.required' => 'Vui lòng chọn hợp đồng.',
            'hop_dong_id.exists' => 'Hợp đồng không tồn tại.',
            'ngay_chup_thuc_te.required' => 'Thiếu ngày chụp chính thức.',
        ]);

        $ngayChup = Carbon::parse($validated['ngay_chup_thuc_te'])->toDateString();
        $hopDongId = (int) $validated['hop_dong_id'];

        $banIds = $this->layTapIdNhanVienBanChoNgayChupThucTe($ngayChup, $hopDongId);
        $messages = [];
        if (! empty($validated['tho_chup_id']) && isset($banIds[(int) $validated['tho_chup_id']])) {
            $messages['tho_chup_id'] = 'Người chụp đã có lịch chụp/make khác vào ngày này.';
        }
        if (! empty($validated['tho_make_id']) && isset($banIds[(int) $validated['tho_make_id']])) {
            $messages['tho_make_id'] = 'Người make đã có lịch chụp/make khác vào ngày này.';
        }
        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }

        if (! empty($validated['gio_chup'])) {
            $validated['gio_chup'] .= ':00';
        }

        unset($validated['hop_dong_id']);

        return DB::transaction(function () use ($validated, $hopDongId) {
            /** @var HopDongCuoi $hd */
            $hd = HopDongCuoi::query()->lockForUpdate()->findOrFail($hopDongId);

            if (in_array($hd->trang_thai_hop_dong ?? '', ['nhap', 'da_huy'], true)) {
                return response()->json(['message' => 'Hợp đồng nháp hoặc đã huỷ nên không thể phân lịch.'], 422);
            }
            if ($hd->ngay_chup_thuc_te || $hd->ngay_chup_du_kien) {
                return response()->json(['message' => 'Hợp đồng đã được phân ngày chụp trước đó.'], 422);
            }

            $hd->fill($validated);
            $hd->save();

            return response()->json(['message' => 'Đã tạo lịch và lưu điều phối.']);
        });
    }

    public function lichLamViecChiTietNgay(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user?->isAdmin() ?? false;
        $nhanVienId = $user?->nhanVien?->id;
        $ngayChupExpr = 'COALESCE(ngay_chup_thuc_te, ngay_chup_du_kien)';
        if (! $isAdmin && ! $nhanVienId) {
            return response()->json(['date' => null, 'items' => []]);
        }

        $date = $request->query('date');
        if (! $date) {
            return response()->json(['date' => null, 'items' => []]);
        }

        $tz = config('app.timezone');
        try {
            $day = Carbon::parse($date, $tz)->toDateString();
        } catch (\Throwable $e) {
            return response()->json(['date' => null, 'items' => []]);
        }

        $locFilters = HopDongCuoiLocTienDoFilter::parseFromRequest($request);

        $items = HopDongCuoi::query()
            ->with([
                'concept',
                'nhomDichVu',
                'thoChup.user',
                'thoMake.user',
                'thoEdit.user',
                'hopDongCuoiTrangPhuc.trangPhuc',
            ])
            ->tap(fn ($q) => $this->lichLamViecExcludeNhap($q))
            ->whereNotNull(DB::raw($ngayChupExpr))
            ->whereRaw("DATE($ngayChupExpr) = ?", [$day])
            ->tap(fn ($q) => HopDongCuoiLocTienDoFilter::apply($q, $locFilters))
            ->when(! $isAdmin, function ($q) use ($nhanVienId) {
                $q->where(function ($qq) use ($nhanVienId) {
                    $qq->where('tho_chup_id', $nhanVienId)
                        ->orWhere('tho_make_id', $nhanVienId)
                        ->orWhere('tho_edit_id', $nhanVienId);
                });
            })
            ->orderByRaw("DATE($ngayChupExpr)")
            ->orderBy('gio_chup')
            ->get()
            ->map(function (HopDongCuoi $hd) use ($nhanVienId, $tz, $isAdmin) {
                return $this->lichLamViecHopDongSummary($hd, $nhanVienId, $isAdmin, $tz);
            })
            ->values();

        return response()->json([
            'date' => $day,
            'items' => $items,
        ]);
    }

    public function congViecCuaToi(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user?->isAdmin() ?? false;
        $nhanVienId = $user?->nhanVien?->id;

        $search = $request->get('search');

        $danhSach = HopDongCuoi::query()
            ->with([
                'thoChup.user',
                'thoMake.user',
                'thoEdit.user',
                'hopDongCuoiTrangPhuc.trangPhuc',
            ])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('ten_chu_re', 'like', "%{$search}%")
                        ->orWhere('ten_co_dau', 'like', "%{$search}%")
                        ->orWhere('email_sdt_chu_re', 'like', "%{$search}%")
                        ->orWhere('email_sdt_co_dau', 'like', "%{$search}%");
                });
            })
            ->where(function ($q) use ($nhanVienId) {
                if (! $nhanVienId) {
                    $q->whereRaw('1=0');

                    return;
                }

                $q->where(function ($qq) use ($nhanVienId) {
                    $qq->where('tho_chup_id', $nhanVienId)
                        ->orWhere('tho_make_id', $nhanVienId)
                        ->orWhere('tho_edit_id', $nhanVienId);
                });
            })
            ->orderByDesc('id')
            ->paginate(AdminPagination::perPage())
            ->withQueryString();

        $conceptMap = Concept::query()
            ->where('trang_thai', Concept::TRANG_THAI_ACTIVE)
            ->orderBy('ten_concept')
            ->get()
            ->keyBy('id');

        return view('admin.nhan-su.cong-viec-cua-toi', compact(
            'danhSach',
            'conceptMap',
            'nhanVienId',
            'isAdmin'
        ));
    }

    public function capNhatLinkFileCongViec(Request $request, HopDongCuoi $hopDongCuoi)
    {
        $user = $request->user();
        $isAdmin = $user?->isAdmin() ?? false;
        $nhanVienId = $user?->nhanVien?->id;

        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['demo', 'in'])],
            'link' => ['nullable', 'string', 'max:500'],
        ], [
            'type.required' => 'Thiếu loại link cần cập nhật.',
            'type.in' => 'Loại link không hợp lệ.',
            'link.max' => 'Link không được quá 500 ký tự.',
        ]);

        $type = $validated['type'];
        $link = trim((string) ($validated['link'] ?? ''));
        $link = $link !== '' ? $link : null;

        if (! $isAdmin) {
            if (! $nhanVienId) {
                abort(403, 'Bạn không có quyền cập nhật link.');
            }

            if ($type === 'demo' && (int) ($hopDongCuoi->tho_chup_id ?? 0) !== (int) $nhanVienId) {
                abort(403, 'Chỉ thợ chụp mới được cập nhật link file chụp.');
            }
            if ($type === 'in' && (int) ($hopDongCuoi->tho_edit_id ?? 0) !== (int) $nhanVienId) {
                abort(403, 'Chỉ thợ edit mới được cập nhật link file edit.');
            }
        }

        if ($type === 'demo') {
            $payload = ['link_demo' => $link];
            if ($link !== null) {
                $payload['ngay_up_link_demo_gan_nhat'] = now();
            }

            $hopDongCuoi->update($payload);

            return back()->with('success', 'Đã cập nhật link file chụp.');
        }

        $payload = ['link_in' => $link];
        if ($link !== null) {
            $payload['ngay_up_link_in_gan_nhat'] = now();
        }

        $hopDongCuoi->update($payload);

        return back()->with('success', 'Đã cập nhật link file edit.');
    }

}

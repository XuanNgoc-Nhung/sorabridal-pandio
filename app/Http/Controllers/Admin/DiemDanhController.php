<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaLamViec;
use App\Models\ChamCong;
use App\Models\DangKyCaLamViec;
use App\Models\DiemDanh;
use App\Models\HopDong;
use App\Models\IpDiemDanh;
use App\Models\NhanVien;
use App\Models\User;
use App\Models\VaiTro;
use App\Models\XinNghiPhep;
use App\Support\AdminPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DiemDanhController extends Controller
{
    public function diemDanh(Request $request)
    {
        $query = DiemDanh::query()
            ->with('user')
            ->where('user_id', Auth::id());

        if ($request->filled('tu_ngay')) {
            $query->whereDate('gio_vao', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('gio_vao', '<=', $request->den_ngay);
        }

        $danhSach = $query->orderByDesc('gio_vao')->paginate(AdminPagination::perPage())->withQueryString();
        $this->ganCaLamHomDoChoDanhSachDiemDanh($danhSach, (int) Auth::id());

        // Trạng thái check-in/check-out của user đăng nhập trong ngày hôm nay
        $canCheckIn = false;
        $canCheckOut = false;
        $showChuaDangKyCaLam = false;
        if (Auth::check()) {
            $userId = Auth::id();
            $hasAnyRecordToday = DiemDanh::query()
                ->where('user_id', $userId)
                ->whereDate('gio_vao', today())
                ->exists();
            $hasOpenRecordToday = DiemDanh::query()
                ->where('user_id', $userId)
                ->whereDate('gio_vao', today())
                ->whereNull('gio_ra')
                ->exists();
            $coDangKyCaLamHomNay = $this->userCoDangKyCaLamHomNay($userId);
            $canCheckIn = ! $hasAnyRecordToday;
            $canCheckOut = $hasOpenRecordToday;
            $showChuaDangKyCaLam = ! $hasAnyRecordToday && ! $coDangKyCaLamHomNay;
        }

        return view('admin.diem-danh.diem-danh', compact(
            'danhSach',
            'canCheckIn',
            'canCheckOut',
            'showChuaDangKyCaLam',
            'coDangKyCaLamHomNay'
        ));
    }

    public function nghiPhep(Request $request)
    {
        $validated = $request->validate([
            'tu_ngay' => 'nullable|date',
            'den_ngay' => 'nullable|date',
            'loai_nghi_phep' => 'nullable|string|in:'.implode(',', array_keys(XinNghiPhep::LOAI_NGHI_PHEP_OPTIONS)),
            'trang_thai' => 'nullable|string|in:'.implode(',', array_keys(XinNghiPhep::TRANG_THAI_OPTIONS)),
        ]);

        $coQuyenDuyet = VaiTro::isAdminMa((string) Auth::user()?->role);

        $query = XinNghiPhep::query()
            ->with(['user', 'nguoiDuyet']);

        if (! $coQuyenDuyet) {
            $query->where('user_id', Auth::id());
        }

        if (! empty($validated['tu_ngay'])) {
            $query->whereDate('ngay_bat_dau', '>=', $validated['tu_ngay']);
        }
        if (! empty($validated['den_ngay'])) {
            $query->whereDate('ngay_bat_dau', '<=', $validated['den_ngay']);
        }
        if (! empty($validated['loai_nghi_phep'])) {
            $query->where('loai_nghi_phep', $validated['loai_nghi_phep']);
        }
        if (! empty($validated['trang_thai'])) {
            $query->where('trang_thai', $validated['trang_thai']);
        }

        $danhSach = $query->orderByDesc('created_at')->paginate(AdminPagination::perPage())->withQueryString();

        return view('admin.diem-danh.nghi-phep', compact('danhSach', 'coQuyenDuyet'));
    }

    public function duyetNghiPhep(XinNghiPhep $xinNghiPhep): RedirectResponse
    {
        if (! VaiTro::isAdminMa((string) Auth::user()?->role)) {
            return redirect()
                ->route('admin.diem-danh.nghi-phep')
                ->with('error', 'Bạn không có quyền duyệt đơn nghỉ phép.');
        }

        if ($xinNghiPhep->trang_thai !== XinNghiPhep::TRANG_THAI_CHO_DUYET) {
            return redirect()
                ->route('admin.diem-danh.nghi-phep')
                ->with('error', 'Chỉ được duyệt đơn đang chờ duyệt.');
        }

        DB::transaction(function () use ($xinNghiPhep) {
            $xinNghiPhep->update([
                'trang_thai' => XinNghiPhep::TRANG_THAI_DA_DUYET,
                'nguoi_duyet' => Auth::id(),
            ]);

            DiemDanh::capNhatTienPhatTuDonNghiPhepDaDuyet($xinNghiPhep);
        });

        return redirect()
            ->route('admin.diem-danh.nghi-phep')
            ->with('success', 'Đã duyệt đơn xin nghỉ phép.');
    }

    public function tuChoiNghiPhep(XinNghiPhep $xinNghiPhep): RedirectResponse
    {
        if (! VaiTro::isAdminMa((string) Auth::user()?->role)) {
            return redirect()
                ->route('admin.diem-danh.nghi-phep')
                ->with('error', 'Bạn không có quyền từ chối đơn nghỉ phép.');
        }

        if ($xinNghiPhep->trang_thai !== XinNghiPhep::TRANG_THAI_CHO_DUYET) {
            return redirect()
                ->route('admin.diem-danh.nghi-phep')
                ->with('error', 'Chỉ được từ chối đơn đang chờ duyệt.');
        }

        $xinNghiPhep->update([
            'trang_thai' => XinNghiPhep::TRANG_THAI_TU_CHOI,
            'nguoi_duyet' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.diem-danh.nghi-phep')
            ->with('success', 'Đã từ chối đơn xin nghỉ phép.');
    }

    public function storeNghiPhep(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'loai_nghi_phep' => ['required', 'string', Rule::in(array_keys(XinNghiPhep::LOAI_NGHI_PHEP_OPTIONS))],
            'buoi_nghi' => [
                'nullable',
                'required_if:loai_nghi_phep,'.XinNghiPhep::LOAI_NUA_NGAY,
                'string',
                Rule::in(array_keys(XinNghiPhep::BUOI_NGHI_OPTIONS)),
            ],
            'ngay_bat_dau' => ['required', 'date'],
            'ngay_ket_thuc' => [
                'nullable',
                'required_if:loai_nghi_phep,'.XinNghiPhep::LOAI_NHIEU_NGAY,
                'date',
                'after_or_equal:ngay_bat_dau',
            ],
            'ly_do' => ['required', 'string', 'max:2000'],
        ], [
            'loai_nghi_phep.required' => 'Vui lòng chọn loại nghỉ phép.',
            'buoi_nghi.required_if' => 'Vui lòng chọn buổi nghỉ.',
            'ngay_bat_dau.required' => 'Vui lòng chọn ngày xin phép hoặc ngày nghỉ.',
            'ngay_ket_thuc.required_if' => 'Vui lòng chọn khoảng ngày nghỉ.',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'ly_do.required' => 'Vui lòng nhập lý do.',
        ]);

        $loai = $validated['loai_nghi_phep'];
        $buoiNghi = $loai === XinNghiPhep::LOAI_NUA_NGAY ? ($validated['buoi_nghi'] ?? null) : null;
        $ngayKetThuc = match ($loai) {
            XinNghiPhep::LOAI_NHIEU_NGAY => $validated['ngay_ket_thuc'] ?? null,
            XinNghiPhep::LOAI_CA_NGAY => $validated['ngay_bat_dau'],
            default => null,
        };

        XinNghiPhep::create([
            'user_id' => Auth::id(),
            'loai_nghi_phep' => $loai,
            'buoi_nghi' => $buoiNghi,
            'ngay_bat_dau' => $validated['ngay_bat_dau'],
            'ngay_ket_thuc' => $ngayKetThuc,
            'ly_do' => trim($validated['ly_do']),
            'trang_thai' => XinNghiPhep::TRANG_THAI_CHO_DUYET,
        ]);

        return redirect()
            ->route('admin.diem-danh.nghi-phep')
            ->with('success', 'Đã gửi đơn xin nghỉ phép.');
    }

    /** @var array<string, string> */
    private const CHAM_CONG_SAP_XEP_OPTIONS = [
        User::SAP_XEP_HO_TEN => 'Họ tên',
        User::SAP_XEP_ID => 'Mới nhất',
    ];

    public function caLam(Request $request)
    {
        $validated = $request->validate([
            'tuan' => 'nullable|date',
            'search' => 'nullable|string|max:255',
        ]);

        $tuanBatDau = ! empty($validated['tuan'])
            ? Carbon::parse($validated['tuan'])->startOfWeek()
            : now()->startOfWeek();
        $tuanKetThuc = (clone $tuanBatDau)->endOfWeek();

        $tuKhoa = trim((string) ($validated['search'] ?? ''));

        $ngayTrongTuan = [];
        for ($d = (clone $tuanBatDau); $d->lte($tuanKetThuc); $d->addDay()) {
            $ngayTrongTuan[] = (clone $d);
        }

        $startStr = $tuanBatDau->toDateString();
        $endStr = $tuanKetThuc->toDateString();

        $nhanVienQuery = User::query()->whereHas('nhanVien');

        if ($tuKhoa !== '') {
            $like = '%'.addcslashes($tuKhoa, '%_\\').'%';
            $nhanVienQuery->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        $nhanVien = $nhanVienQuery->orderBy('name')->get();

        $dangKy = DangKyCaLamViec::query()
            ->with('caLamViec')
            ->whereBetween('ngay_lam', [$startStr, $endStr])
            ->get();

        $bangCaLam = [];
        foreach ($dangKy as $record) {
            $dateKey = $record->ngay_lam->format('Y-m-d');
            $bangCaLam[$dateKey][$record->nguoi_dung_id][] = $record;
        }

        $danhSachCaLamViec = CaLamViec::query()
            ->orderBy('gio_bat_dau')
            ->get();

        $caLamTheoNhanVien = [];
        foreach ($nhanVien as $user) {
            $caIds = [];
            foreach ($ngayTrongTuan as $day) {
                $records = $bangCaLam[$day->toDateString()][$user->id] ?? [];
                foreach ($records as $record) {
                    $caIds[$record->ca_lam_id] = true;
                }
            }
            $uniqueCaIds = array_keys($caIds);
            $caLamTheoNhanVien[$user->id] = count($uniqueCaIds) === 1 ? $uniqueCaIds[0] : null;
        }

        return view('admin.diem-danh.ca-lam', [
            'tuan' => $tuanBatDau->toDateString(),
            'tuanBatDau' => $tuanBatDau,
            'tuanKetThuc' => $tuanKetThuc,
            'ngayTrongTuan' => $ngayTrongTuan,
            'nhanVien' => $nhanVien,
            'bangCaLam' => $bangCaLam,
            'danhSachCaLamViec' => $danhSachCaLamViec,
            'caLamTheoNhanVien' => $caLamTheoNhanVien,
            'search' => $tuKhoa,
        ]);
    }

    public function capNhatCaLamTuan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nguoi_dung_id' => ['required', 'integer', 'exists:users,id'],
            'tuan' => ['required', 'date'],
            'ca_lam_id' => ['nullable', 'integer', 'exists:ca_lam_viec,id'],
        ]);

        $user = User::query()
            ->whereHas('nhanVien')
            ->find($validated['nguoi_dung_id']);

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => 'Nhân viên không hợp lệ.',
            ], 422);
        }

        $tuanBatDau = Carbon::parse($validated['tuan'])->startOfWeek();
        $tuanKetThuc = (clone $tuanBatDau)->endOfWeek();
        $caLamId = $validated['ca_lam_id'] ?? null;

        $caLam = null;
        if ($caLamId !== null) {
            $caLam = CaLamViec::query()->find($caLamId);
            if ($caLam === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ca làm không tồn tại.',
                ], 422);
            }
        }

        DB::transaction(function () use ($user, $tuanBatDau, $tuanKetThuc, $caLamId) {
            if ($caLamId === null) {
                DangKyCaLamViec::query()
                    ->where('nguoi_dung_id', $user->id)
                    ->whereBetween('ngay_lam', [$tuanBatDau->toDateString(), $tuanKetThuc->toDateString()])
                    ->delete();

                return;
            }

            for ($d = (clone $tuanBatDau); $d->lte($tuanKetThuc); $d->addDay()) {
                $this->dongBoDangKyCaLamChoNgay($user->id, $d->toDateString(), $caLamId);
            }
        });

        return response()->json([
            'success' => true,
            'message' => $caLamId === null
                ? 'Đã xóa ca làm trong tuần.'
                : 'Đã cập nhật ca làm cho cả tuần.',
            'ca_lam' => $this->formatCaLamJson($caLam),
        ]);
    }

    public function capNhatCaLamNgay(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nguoi_dung_id' => ['required', 'integer', 'exists:users,id'],
            'ngay_lam' => ['required', 'date'],
            'ca_lam_id' => ['nullable', 'integer', 'exists:ca_lam_viec,id'],
        ]);

        $user = User::query()
            ->whereHas('nhanVien')
            ->find($validated['nguoi_dung_id']);

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => 'Nhân viên không hợp lệ.',
            ], 422);
        }

        $ngayLam = Carbon::parse($validated['ngay_lam'])->toDateString();
        $caLamId = $validated['ca_lam_id'] ?? null;

        $caLam = null;
        if ($caLamId !== null) {
            $caLam = CaLamViec::query()->find($caLamId);
            if ($caLam === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ca làm không tồn tại.',
                ], 422);
            }
        }

        DB::transaction(function () use ($user, $ngayLam, $caLamId) {
            $this->dongBoDangKyCaLamChoNgay($user->id, $ngayLam, $caLamId);
        });

        return response()->json([
            'success' => true,
            'message' => $caLamId === null
                ? 'Đã xóa ca làm trong ngày.'
                : 'Đã cập nhật ca làm cho ngày.',
            'ca_lam' => $this->formatCaLamJson($caLam),
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function formatCaLamJson(?CaLamViec $caLam): ?array
    {
        if ($caLam === null) {
            return null;
        }

        return [
            'id' => $caLam->id,
            'ten_ca' => $caLam->ten_ca,
            'gio_bat_dau' => CaLamViec::formatGio($caLam->gio_bat_dau),
            'gio_ket_thuc' => CaLamViec::formatGio($caLam->gio_ket_thuc),
        ];
    }

    private function dongBoDangKyCaLamChoNgay(int $nguoiDungId, string $ngayLam, ?int $caLamId): void
    {
        $dangKyHienCo = DangKyCaLamViec::query()
            ->where('nguoi_dung_id', $nguoiDungId)
            ->whereDate('ngay_lam', $ngayLam)
            ->orderBy('id')
            ->get();

        if ($caLamId === null) {
            if ($dangKyHienCo->isNotEmpty()) {
                DangKyCaLamViec::query()
                    ->where('nguoi_dung_id', $nguoiDungId)
                    ->whereDate('ngay_lam', $ngayLam)
                    ->delete();
            }

            return;
        }

        $banGhi = $dangKyHienCo->first();

        if ($banGhi !== null) {
            if ((int) $banGhi->ca_lam_id !== $caLamId) {
                $banGhi->update(['ca_lam_id' => $caLamId]);
            }

            if ($dangKyHienCo->count() > 1) {
                DangKyCaLamViec::query()
                    ->where('nguoi_dung_id', $nguoiDungId)
                    ->whereDate('ngay_lam', $ngayLam)
                    ->where('id', '!=', $banGhi->id)
                    ->delete();
            }

            return;
        }

        DangKyCaLamViec::create([
            'ca_lam_id' => $caLamId,
            'nguoi_dung_id' => $nguoiDungId,
            'ngay_lam' => $ngayLam,
        ]);
    }

    public function chamCong(Request $request)
    {
        $validated = $request->validate([
            'thang' => 'nullable|date_format:Y-m',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2100',
            'user_id' => 'nullable|integer|exists:users,id',
            'trang_thai' => 'nullable|in:da_cham,chua_cham',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(self::CHAM_CONG_SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        if (! empty($validated['thang'])) {
            $thangCarbon = Carbon::createFromFormat('Y-m', $validated['thang']);
            $month = $thangCarbon->month;
            $year = $thangCarbon->year;
        } else {
            $month = (int) ($validated['month'] ?? now()->month);
            $year = (int) ($validated['year'] ?? now()->year);
        }

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }
        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $thang = sprintf('%04d-%02d', $year, $month);

        $sapXepTheo = $validated['sap_xep_theo'] ?? User::SAP_XEP_HO_TEN;
        if (! array_key_exists($sapXepTheo, self::CHAM_CONG_SAP_XEP_OPTIONS)) {
            $sapXepTheo = User::SAP_XEP_HO_TEN;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';
        $trangThai = $validated['trang_thai'] ?? '';
        $userIdLoc = isset($validated['user_id']) ? (int) $validated['user_id'] : null;

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $ngayTrongThang = [];
        for ($d = (clone $start); $d->lte($end); $d->addDay()) {
            $ngayTrongThang[] = (clone $d);
        }

        $startStr = $start->format('Y-m-d');
        $endStr = $end->format('Y-m-d');

        $userIdsCoChamCongTrongThang = ChamCong::query()
            ->whereBetween('ngay_diem_danh', [$startStr, $endStr])
            ->distinct()
            ->pluck('user_id');

        $nhanVienQuery = User::query()->whereHas('nhanVien');

        if ($userIdLoc !== null) {
            $nhanVienQuery->where('id', $userIdLoc);
        }

        if ($trangThai === 'da_cham') {
            $nhanVienQuery->whereIn('id', $userIdsCoChamCongTrongThang);
        } elseif ($trangThai === 'chua_cham') {
            $nhanVienQuery->whereNotIn('id', $userIdsCoChamCongTrongThang);
        }

        if ($sapXepTheo === User::SAP_XEP_HO_TEN) {
            $nhanVienQuery->orderBy('name', $thuTu);
        } else {
            $nhanVienQuery->orderBy('id', $thuTu);
        }

        $nhanVien = $nhanVienQuery->get();

        $chamCong = ChamCong::query()
            ->with(['user', 'diemDanh'])
            ->whereBetween('ngay_diem_danh', [$startStr, $endStr])
            ->get();

        $bangChamCong = [];
        foreach ($chamCong as $record) {
            $date = $record->ngay_diem_danh;
            $dateKey = $date ? Carbon::parse($date)->format('Y-m-d') : null;
            if (! $dateKey) {
                continue;
            }
            $bangChamCong[$dateKey][$record->user_id] = $record;
        }

        $danhSachNhanVienLoc = User::query()
            ->whereHas('nhanVien')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.diem-danh.cham-cong', [
            'thang' => $thang,
            'month' => $month,
            'year' => $year,
            'start' => $start,
            'end' => $end,
            'ngayTrongThang' => $ngayTrongThang,
            'nhanVien' => $nhanVien,
            'bangChamCong' => $bangChamCong,
            'danhSachNhanVienLoc' => $danhSachNhanVienLoc,
            'userIdLoc' => $userIdLoc,
            'trangThai' => $trangThai,
            'sapXepTheo' => $sapXepTheo,
            'thuTu' => $thuTu,
            'chamCongSapXepOptions' => self::CHAM_CONG_SAP_XEP_OPTIONS,
        ]);
    }

    /**
     * Ghi nhận giờ vào (check-in) cho user đăng nhập.
     */
    public function checkIn(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return $this->jsonDiemDanhError('Vui lòng đăng nhập để điểm danh.', 401);
        }

        $userId = Auth::id();

        $exists = DiemDanh::query()
            ->where('user_id', $userId)
            ->whereDate('gio_vao', today())
            ->exists();

        if ($exists) {
            return $this->jsonDiemDanhError('Bạn đã điểm danh vào hôm nay rồi.');
        }

        if (! $this->userCoDangKyCaLamHomNay($userId)) {
            return $this->jsonDiemDanhError('Bạn chưa được phân ca làm việc hôm nay. Không thể điểm danh.');
        }

        $validated = $request->validate([
            'client_ip' => 'required|string|max:45',
        ]);

        if ($guardResponse = $this->guardDiemDanhPublicIp(
            'Check-in',
            $validated['client_ip'],
            IpDiemDanh::diaChiIpAllowlistDangHoatDong(),
            'Chỉ được điểm danh khi kết nối từ mạng được phép (IP hiện tại không nằm trong danh sách).',
            true
        )) {
            return $guardResponse;
        }

        $gioVao = now();
        $thoiGianDiMuon = $this->tinhThoiGianDiMuon($userId, $gioVao);
        $tienPhatDiMuon = $this->tinhTienPhatTheoSoPhut($thoiGianDiMuon);
        $clientIp = trim($validated['client_ip']);

        DB::transaction(function () use ($userId, $gioVao, $thoiGianDiMuon, $tienPhatDiMuon, $clientIp) {
            $diemDanh = DiemDanh::create([
                'user_id' => $userId,
                'gio_vao' => $gioVao,
                'gio_ra' => null,
                'di_muon' => $thoiGianDiMuon > 0,
                'thoi_gian_di_muon' => $thoiGianDiMuon,
                'tien_phat_di_muon' => $tienPhatDiMuon,
                'ip_checkin' => $clientIp,
            ]);

            ChamCong::query()->updateOrCreate(
                [
                    'user_id' => $userId,
                    'ngay_diem_danh' => today()->toDateString(),
                ],
                [
                    'diem_danh_id' => $diemDanh->id,
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => $this->diemDanhMessageWithIp(
                'Check-in thành công lúc '.$gioVao->format('H:i d/m/Y').'.',
                $clientIp
            ),
            'client_ip' => $clientIp,
            'gio_vao' => $gioVao->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Ghi nhận giờ ra (check-out) cho user đăng nhập.
     * Tính giờ làm cơ bản (từ giờ vào đến 21:00), giờ tăng ca (từ 21:00 đến giờ ra),
     * và lương cơ bản, lương tăng ca theo đơn giá ở bảng nhan_vien.
     */
    public function checkOut(Request $request): JsonResponse
    {
        Log::info('Check-out: yêu cầu từ user', ['user_id' => Auth::id()]);

        if (! Auth::check()) {
            Log::warning('Check-out: thất bại - chưa đăng nhập');

            return $this->jsonDiemDanhError('Vui lòng đăng nhập.', 401);
        }

        $record = DiemDanh::query()
            ->where('user_id', Auth::id())
            ->whereDate('gio_vao', today())
            ->whereNull('gio_ra')
            ->first();

        if (! $record) {
            Log::warning('Check-out: không có bản ghi check-in hôm nay hoặc đã check-out', [
                'user_id' => Auth::id(),
                'today' => today()->toDateString(),
            ]);

            return $this->jsonDiemDanhError('Chưa có bản ghi check-in hôm nay hoặc đã check-out rồi.');
        }

        $validated = $request->validate([
            'client_ip' => 'required|string|max:45',
        ]);

        if ($guardResponse = $this->guardDiemDanhPublicIp(
            'Check-out',
            $validated['client_ip'],
            IpDiemDanh::diaChiIpAllowlistDangHoatDong(),
            'Chỉ được check-out khi kết nối từ mạng được phép (IP hiện tại không nằm trong danh sách).',
            true
        )) {
            return $guardResponse;
        }

        $gioRa = Carbon::now();
        $gioVao = Carbon::parse($record->gio_vao);
        $cuoiGioCoBan = Carbon::parse($gioVao->toDateString().' 21:00:00');

        // Giờ làm cơ bản: từ gio_vao đến min(gio_ra, 21:00). Nếu ra trước 21:00 thì toàn bộ là cơ bản.
        if ($gioRa->lte($cuoiGioCoBan)) {
            $gioLamCoBan = round($gioVao->diffInMinutes($gioRa) / 60, 2);
            $gioLamTangCa = 0.0;
        } else {
            $gioLamCoBan = round($gioVao->diffInMinutes($cuoiGioCoBan) / 60, 2);
            $gioLamTangCa = round($cuoiGioCoBan->diffInMinutes($gioRa) / 60, 2);
        }

        // Lấy đơn giá lương từ nhan_vien (theo user_id)
        $nhanVien = NhanVien::query()->where('user_id', $record->user_id)->first();
        $donGiaLuongCoBan = $nhanVien ? (float) $nhanVien->luong_co_ban : 0;
        $donGiaLuongTangCa = $nhanVien ? (float) $nhanVien->luong_tang_ca : 0;

        $luongCoBan = round($gioLamCoBan * $donGiaLuongCoBan, 2);
        $luongTangCa = round($gioLamTangCa * $donGiaLuongTangCa, 2);
        $thoiGianVeSom = $this->tinhThoiGianVeSom($record->user_id, $gioRa);
        $tienPhatVeSom = $this->tinhTienPhatTheoSoPhut($thoiGianVeSom);
        $clientIp = trim($validated['client_ip']);

        $record->update([
            'gio_ra' => $gioRa,
            'thoi_gian_ve_som' => $thoiGianVeSom,
            'tien_phat_ve_som' => $tienPhatVeSom,
            'ip_checkout' => $clientIp,
            'gio_lam_co_ban' => $gioLamCoBan,
            'gio_lam_tang_ca' => $gioLamTangCa,
            'luong_co_ban' => $luongCoBan,
            'luong_tang_ca' => $luongTangCa,
        ]);

        Log::info('Check-out: thành công', [
            'user_id' => Auth::id(),
            'diem_danh_id' => $record->id,
            'gio_vao' => $gioVao->toDateTimeString(),
            'gio_ra' => $gioRa->toDateTimeString(),
            'gio_lam_co_ban' => $gioLamCoBan,
            'gio_lam_tang_ca' => $gioLamTangCa,
            'luong_co_ban' => $luongCoBan,
            'luong_tang_ca' => $luongTangCa,
        ]);

        return response()->json([
            'success' => true,
            'message' => $this->diemDanhMessageWithIp(
                'Check-out thành công lúc '.$gioRa->format('H:i d/m/Y').'.',
                $clientIp
            ),
            'client_ip' => $clientIp,
            'gio_ra' => $gioRa->format('d/m/Y H:i'),
        ]);
    }

    // Điều phối công việc (chỉ xem danh sách hợp đồng, không thêm/sửa)
    public function dieuPhoiCongViec(Request $request)
    {
        $search = $request->get('search');
        $danhSach = HopDong::query()
            ->with(['nguoiTao', 'thoChup.user', 'thoMake.user', 'thoEdit.user'])
            ->when($search, function ($q) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $q->where(function ($q2) use ($like) {
                    $q2->where('ma_hop_dong', 'like', $like)
                        ->orWhere('dia_diem', 'like', $like)
                        ->orWhere('concept', 'like', $like)
                        ->orWhere('ghi_chu_chup', 'like', $like)
                        ->orWhere('trang_phuc', 'like', $like);
                });
            })
            ->orderByDesc('id')
            ->paginate(AdminPagination::perPage())
            ->withQueryString();

        $danhSachNhanVien = NhanVien::query()->with('user')->orderBy('id')->get();

        return view('admin.diem-danh.dieu-phoi-cong-viec', compact('danhSach', 'danhSachNhanVien'));
    }

    /**
     * Cập nhật phân công thợ chụp / make / edit cho hợp đồng (từ modal Phân việc).
     */
    public function phanCongCongViec(Request $request, HopDong $hopDong)
    {
        $validated = $request->validate([
            'tho_chup_id' => 'nullable|exists:nhan_vien,id',
            'tho_make_id' => 'nullable|exists:nhan_vien,id',
            'tho_edit_id' => 'nullable|exists:nhan_vien,id',
            'dia_diem' => 'nullable|string|max:255',
            'trang_phuc' => 'nullable|string|max:255',
            'ngay_chup' => 'nullable|date',
            'ngay_hen_tra_hang' => 'nullable|date',
            'ngay_tra_link_in' => 'nullable|date',
        ]);

        $hopDong->update([
            'tho_chup_id' => $validated['tho_chup_id'] ?? null,
            'tho_make_id' => $validated['tho_make_id'] ?? null,
            'tho_edit_id' => $validated['tho_edit_id'] ?? null,
            'dia_diem' => $validated['dia_diem'] ?? null,
            'trang_phuc' => $validated['trang_phuc'] ?? null,
            'ngay_chup' => $validated['ngay_chup'] ?? null,
            'ngay_hen_tra_hang' => $validated['ngay_hen_tra_hang'] ?? null,
            'ngay_tra_link_in' => $validated['ngay_tra_link_in'] ?? null,
        ]);

        return redirect()->route('admin.diem-danh.dieu-phoi-cong-viec')->with('success', 'Phân công công việc đã được cập nhật.');
    }

    public function storeDieuPhoiCongViec(Request $request)
    {
        return redirect()->route('admin.diem-danh.dieu-phoi-cong-viec')->with('success', 'Điều phối công việc thành công.');
    }

    public function updateDieuPhoiCongViec(Request $request, $dieuPhoiCongViec)
    {
        return redirect()->route('admin.diem-danh.dieu-phoi-cong-viec')->with('success', 'Điều phối công việc thành công.');
    }

    public function destroyDieuPhoiCongViec(Request $request, $dieuPhoiCongViec)
    {
        return redirect()->route('admin.diem-danh.dieu-phoi-cong-viec')->with('success', 'Điều phối công việc thành công.');
    }

    /**
     * Chặn điểm danh / check-out nếu IP công cộng do trình duyệt gửi lên không nằm trong allowlist.
     *
     * @param  array<string, string>  $allowlist
     * @return RedirectResponse|JsonResponse|null null nếu được phép tiếp tục
     */
    private function guardDiemDanhPublicIp(
        string $hanhDong,
        string $clientIp,
        array $allowlist,
        string $ipKhongHopLeMessage,
        bool $asJson = false
    ): RedirectResponse|JsonResponse|null {
        $allowed = [];
        foreach ($allowlist as $key => $ip) {
            $ip = trim((string) $ip);
            if ($ip !== '') {
                $allowed[$key] = $ip;
            }
        }

        $fail = function (string $message, ?string $ipForMessage = null) use ($asJson): RedirectResponse|JsonResponse {
            $messageWithIp = $this->diemDanhMessageWithIp($message, $ipForMessage);

            if ($asJson) {
                return $this->jsonDiemDanhError($messageWithIp, 422, $ipForMessage);
            }

            return redirect()->route('admin.diem-danh.diem-danh')->with('error', $messageWithIp);
        };

        if ($allowed === []) {
            Log::warning("{$hanhDong}: allowlist IP không có value hợp lệ.", [
                'user_id' => Auth::id(),
            ]);

            return $fail('Điểm danh chưa được cấu hình. Liên hệ quản trị.');
        }

        $allowedValues = array_values($allowed);
        $ip = trim($clientIp);

        if ($ip === '' || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            Log::warning("{$hanhDong}: client_ip không phải IPv4 hợp lệ", [
                'user_id' => Auth::id(),
                'client_ip' => $clientIp,
            ]);

            return $fail(
                'Không xác minh được địa chỉ IPv4 hiện tại. Thử lại sau hoặc liên hệ quản trị.',
                trim($clientIp) !== '' ? trim($clientIp) : null
            );
        }

        Log::info("{$hanhDong}: địa chỉ IP (từ trình duyệt)", [
            'user_id' => Auth::id(),
            'ip' => $ip,
        ]);

        if (! in_array($ip, $allowedValues, true)) {
            Log::warning("{$hanhDong}: IP không nằm trong allowlist", [
                'user_id' => Auth::id(),
                'ip' => $ip,
                'allowlist_keys' => array_keys($allowed),
            ]);

            return $fail($ipKhongHopLeMessage, $ip);
        }

        return null;
    }

    private function diemDanhMessageWithIp(string $message, ?string $clientIp = null): string
    {
        $ip = trim((string) $clientIp);
        if ($ip === '') {
            return $message;
        }

        $suffix = " IP hiện tại: {$ip}.";

        return rtrim($message, '.').$suffix;
    }

    private function userCoDangKyCaLamHomNay(int $userId): bool
    {
        return DangKyCaLamViec::query()
            ->where('nguoi_dung_id', $userId)
            ->whereDate('ngay_lam', today())
            ->exists();
    }

    /**
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, DiemDanh>  $danhSach
     */
    private function ganCaLamHomDoChoDanhSachDiemDanh($danhSach, int $userId): void
    {
        $collection = $danhSach->getCollection();
        if ($collection->isEmpty()) {
            return;
        }

        $dates = $collection
            ->map(fn (DiemDanh $item) => $item->gio_vao?->toDateString())
            ->filter()
            ->unique()
            ->values();

        if ($dates->isEmpty()) {
            return;
        }

        $dangKyTheoNgay = DangKyCaLamViec::query()
            ->with('caLamViec')
            ->where('nguoi_dung_id', $userId)
            ->whereIn('ngay_lam', $dates)
            ->get()
            ->keyBy(fn (DangKyCaLamViec $dk) => $dk->ngay_lam->format('Y-m-d'));

        $danhSach->setCollection(
            $collection->map(function (DiemDanh $item) use ($dangKyTheoNgay) {
                $ngay = $item->gio_vao?->format('Y-m-d');
                $item->caLamHomDo = $ngay ? $dangKyTheoNgay->get($ngay)?->caLamViec : null;

                return $item;
            })
        );
    }

    private function dangKyCaLamHomNay(int $userId): ?DangKyCaLamViec
    {
        return DangKyCaLamViec::query()
            ->with('caLamViec')
            ->where('nguoi_dung_id', $userId)
            ->whereDate('ngay_lam', today())
            ->first();
    }

    /**
     * Số phút đi muộn so với giờ bắt đầu ca làm hôm nay (0 nếu vào đúng giờ hoặc sớm hơn).
     */
    private function tinhThoiGianDiMuon(int $userId, Carbon $gioVao): int
    {
        $dangKy = $this->dangKyCaLamHomNay($userId);
        $caLam = $dangKy?->caLamViec;

        if ($caLam === null || $caLam->gio_bat_dau === null) {
            return 0;
        }

        $gioBatDauCa = Carbon::parse($gioVao->toDateString().' '.$caLam->gio_bat_dau);

        if ($gioVao->lte($gioBatDauCa)) {
            return 0;
        }

        return (int) $gioBatDauCa->diffInMinutes($gioVao);
    }

    /**
     * Số phút về sớm so với giờ kết thúc ca làm hôm nay (0 nếu ra đúng giờ hoặc muộn hơn).
     */
    private function tinhThoiGianVeSom(int $userId, Carbon $gioRa): int
    {
        $dangKy = $this->dangKyCaLamHomNay($userId);
        $caLam = $dangKy?->caLamViec;

        if ($caLam === null || $caLam->gio_ket_thuc === null) {
            return 0;
        }

        $gioKetThucCa = Carbon::parse($gioRa->toDateString().' '.$caLam->gio_ket_thuc);

        if ($gioRa->gte($gioKetThucCa)) {
            return 0;
        }

        return (int) $gioRa->diffInMinutes($gioKetThucCa);
    }

    /**
     * Tiền phạt theo số phút đi muộn / về sớm: 1–30 phút = 50.000đ, trên 30 phút = 100.000đ.
     */
    private function tinhTienPhatTheoSoPhut(int $soPhut): int
    {
        if ($soPhut <= 0) {
            return 0;
        }

        if ($soPhut <= 30) {
            return 50000;
        }

        return 100000;
    }

    private function jsonDiemDanhError(string $message, int $status = 422, ?string $clientIp = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        $ip = trim((string) $clientIp);
        if ($ip !== '') {
            $payload['client_ip'] = $ip;
        }

        return response()->json($payload, $status);
    }
}

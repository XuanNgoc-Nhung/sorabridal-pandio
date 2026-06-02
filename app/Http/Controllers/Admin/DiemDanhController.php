<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChamCong;
use App\Models\DiemDanh;
use App\Models\HopDong;
use App\Models\NhanVien;
use App\Models\User;
use App\Support\AdminPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiemDanhController extends Controller
{
    /**
     * IP công cộng (theo https://api.myip.com) được phép check-in / check-out.
     * So khớp với trường `ip` trong JSON phản hồi của API.
     *
     * | key (gợi nhớ) | value (IP) |
     * |---------------|------------|
     * | van_phong     | 1.2.3.4    |
     * | chi_nhanh     | 5.6.7.8    |
     *
     * Thay value bằng IP thật (xem log sau lần gọi API hoặc mở api.myip.com trên cùng mạng với máy chủ).
     */
    private const DIEM_DANH_IP_ALLOWLIST = [
        'van_phong' => '1.2.3.4',
        'chi_nhanh' => '5.6.7.8',
        'chi_nhanh_2' => '14.231.244.24',
    ];

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

        // Trạng thái check-in/check-out của user đăng nhập trong ngày hôm nay
        $canCheckIn = false;
        $canCheckOut = false;
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
            $canCheckIn = ! $hasAnyRecordToday;
            $canCheckOut = $hasOpenRecordToday;
        }

        return view('admin.diem-danh.diem-danh', compact('danhSach', 'canCheckIn', 'canCheckOut'));
    }

    public function chamCong(Request $request)
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

        $ngayTrongThang = [];
        for ($d = (clone $start); $d->lte($end); $d->addDay()) {
            $ngayTrongThang[] = (clone $d);
        }

        $startStr = $start->format('Y-m-d');
        $endStr = $end->format('Y-m-d');

        // User có chấm công trong tháng (bất kể role) để đảm bảo hiển thị đủ
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
        foreach ($chamCong as $record) {
            $date = $record->ngay_diem_danh;
            $dateKey = $date ? Carbon::parse($date)->format('Y-m-d') : null;
            if (! $dateKey) {
                continue;
            }
            $bangChamCong[$dateKey][$record->user_id] = $record;
        }

        return view('admin.diem-danh.cham-cong', [
            'month' => $month,
            'year' => $year,
            'start' => $start,
            'end' => $end,
            'ngayTrongThang' => $ngayTrongThang,
            'nhanVien' => $nhanVien,
            'bangChamCong' => $bangChamCong,
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

        if ($guardResponse = $this->guardDiemDanhPublicIp('Check-in', true)) {
            return $guardResponse;
        }

        $gioVao = now();

        DB::transaction(function () use ($userId, $gioVao) {
            $diemDanh = DiemDanh::create([
                'user_id' => $userId,
                'gio_vao' => $gioVao,
                'gio_ra' => null,
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
            'message' => 'Check-in thành công lúc '.$gioVao->format('H:i d/m/Y').'.',
            'gio_vao' => $gioVao->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Ghi nhận giờ ra (check-out) cho user đăng nhập.
     * Tính giờ làm cơ bản (từ giờ vào đến 21:00), giờ tăng ca (từ 21:00 đến giờ ra),
     * và lương cơ bản, lương tăng ca theo đơn giá ở bảng nhan_vien.
     */
    public function checkOut(Request $request)
    {
        Log::info('Check-out: yêu cầu từ user', ['user_id' => Auth::id()]);

        if (! Auth::check()) {
            Log::warning('Check-out: thất bại - chưa đăng nhập');

            return redirect()->route('admin.diem-danh.diem-danh')->with('error', 'Vui lòng đăng nhập.');
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

            return redirect()->route('admin.diem-danh.diem-danh')->with('error', 'Chưa có bản ghi check-in hôm nay hoặc đã check-out rồi.');
        }

        if ($redirect = $this->guardDiemDanhPublicIp('Check-out')) {
            return $redirect;
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

        $record->update([
            'gio_ra' => $gioRa,
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

        return redirect()->route('admin.diem-danh.diem-danh')->with('success', 'Check-out thành công lúc '.$gioRa->format('H:i d/m/Y').'.');
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
     * Chặn điểm danh nếu IP công cộng (api.myip.com) không nằm trong {@see self::DIEM_DANH_IP_ALLOWLIST}.
     *
     * @return RedirectResponse|JsonResponse|null null nếu được phép tiếp tục
     */
    private function guardDiemDanhPublicIp(string $hanhDong, bool $asJson = false): RedirectResponse|JsonResponse|null
    {
        $allowed = [];
        foreach (self::DIEM_DANH_IP_ALLOWLIST as $key => $ip) {
            $ip = trim((string) $ip);
            if ($ip !== '') {
                $allowed[$key] = $ip;
            }
        }

        $fail = function (string $message) use ($asJson): RedirectResponse|JsonResponse {
            if ($asJson) {
                return $this->jsonDiemDanhError($message);
            }

            return redirect()->route('admin.diem-danh.diem-danh')->with('error', $message);
        };

        if ($allowed === []) {
            Log::warning("{$hanhDong}: DIEM_DANH_IP_ALLOWLIST không có value IP hợp lệ.", [
                'user_id' => Auth::id(),
            ]);

            return $fail('Điểm danh chưa được cấu hình (danh sách IP cho phép trống). Liên hệ quản trị.');
        }

        $allowedValues = array_values($allowed);

        try {
            $response = Http::timeout(3)->get('https://api.myip.com');
            if (! $response->successful()) {
                Log::warning("{$hanhDong}: api.myip.com trả về lỗi", [
                    'user_id' => Auth::id(),
                    'status' => $response->status(),
                ]);

                return $fail('Không xác minh được mạng hiện tại. Thử lại sau hoặc liên hệ quản trị.');
            }

            $payload = $response->json();
            $ip = is_array($payload) ? trim((string) ($payload['ip'] ?? '')) : '';

            if ($ip === '') {
                Log::warning("{$hanhDong}: api.myip.com không trả về IP", [
                    'user_id' => Auth::id(),
                    'payload' => $payload,
                ]);

                return $fail('Không xác minh được mạng hiện tại. Thử lại sau hoặc liên hệ quản trị.');
            }

            Log::info("{$hanhDong}: địa chỉ IP (api.myip.com)", [
                'user_id' => Auth::id(),
                'ip' => $ip,
                'country' => is_array($payload) ? ($payload['country'] ?? null) : null,
                'cc' => is_array($payload) ? ($payload['cc'] ?? null) : null,
            ]);

            if (! in_array($ip, $allowedValues, true)) {
                Log::warning("{$hanhDong}: IP không nằm trong allowlist", [
                    'user_id' => Auth::id(),
                    'ip' => $ip,
                    'allowlist_keys' => array_keys($allowed),
                ]);

                return $fail('Chỉ được điểm danh khi kết nối từ mạng được phép (IP hiện tại không nằm trong danh sách).');
            }
        } catch (\Throwable $e) {
            Log::warning("{$hanhDong}: không gọi được api.myip.com", [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            return $fail('Không xác minh được mạng hiện tại. Thử lại sau hoặc liên hệ quản trị.');
        }

        return null;
    }

    private function jsonDiemDanhError(string $message, int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}

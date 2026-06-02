<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\DichVuLe;
use App\Models\HopDongChoThueTrangPhuc;
use App\Models\HopDongCuoi;
use App\Models\HopDongThanhToan;
use App\Models\NhanVien;
use App\Models\PhongBan;
use App\Models\TrangPhuc;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tu_ngay' => ['nullable', 'date'],
            'den_ngay' => [
                'nullable',
                'date',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    $tu = $request->input('tu_ngay');
                    if ($tu && $value && strtotime((string) $value) < strtotime((string) $tu)) {
                        $fail('Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.');
                    }
                },
            ],
        ]);

        $denNgayDefault = now()->endOfDay();
        $tuNgayDefault = $denNgayDefault->copy()->startOfYear()->startOfDay();

        if ($validator->fails()) {
            $tuNgay = $tuNgayDefault;
            $denNgay = $denNgayDefault;
        } else {
            $validated = $validator->validated();
            $denNgay = isset($validated['den_ngay'])
                ? Carbon::parse($validated['den_ngay'])->endOfDay()
                : now()->endOfDay();
            $tuNgay = isset($validated['tu_ngay'])
                ? Carbon::parse($validated['tu_ngay'])->startOfDay()
                : $denNgay->copy()->startOfYear()->startOfDay();
        }

        $soHopDongCuoi = HopDongCuoi::query()->count();
        $soHopDongThueTrangPhuc = HopDongChoThueTrangPhuc::query()->count();
        $soKhachHang = 0;
        $soNhanVien = NhanVien::query()->count();
        $soTrangPhuc = TrangPhuc::query()->count();

        $thongKeNhanVienTheoPhongBan = PhongBan::query()
            ->withCount('nhanViens')
            ->orderBy('ten_phong_ban')
            ->get();

        $soNhanVienChuaGanPhongBan = NhanVien::query()
            ->whereDoesntHave('phongBans')
            ->count();

        $doanhThuCuoiTrongKy = (float) HopDongThanhToan::query()
            ->whereBetween('ngay_thanh_toan', [$tuNgay->toDateString(), $denNgay->toDateString()])
            ->sum('so_tien');

        $doanhThuThueTrongKy = (float) HopDongChoThueTrangPhuc::query()
            ->where('trang_thai', '!=', 2)
            ->whereBetween('created_at', [$tuNgay, $denNgay])
            ->sum('tong_tien');

        $tongGiaTriHopDongCuoi = (float) HopDongCuoi::query()
            ->where('trang_thai_hop_dong', '!=', 'da_huy')
            ->sum('tong_tien');

        $tongDaThuCuoi = (float) HopDongThanhToan::query()->sum('so_tien');

        $trangThaiCuoiTongQuanLabels = [
            'nhap' => 'Nháp',
            'da_huy' => 'Đã huỷ',
            'dang_thuc_hien' => 'Đang thực hiện',
            'tre_chup' => 'Trễ chụp',
            'tre_edit' => 'Trễ edit',
        ];

        $thongKeTrangThaiCuoi = HopDongCuoi::query()
            ->selectRaw('trang_thai_hop_dong as tt, COUNT(*) as so_luong')
            ->groupBy('trang_thai_hop_dong')
            ->pluck('so_luong', 'tt')
            ->all();

        $trangThaiThueLabels = [
            0 => 'Đang diễn ra',
            1 => 'Hoàn thành',
            2 => 'Đã huỷ',
        ];

        $thongKeTrangThaiThueRaw = HopDongChoThueTrangPhuc::query()
            ->selectRaw('trang_thai as tt, COUNT(*) as so_luong')
            ->groupBy('trang_thai')
            ->pluck('so_luong', 'tt')
            ->all();
        $thongKeTrangThaiThue = [];
        foreach ($thongKeTrangThaiThueRaw as $k => $v) {
            $thongKeTrangThaiThue[(int) $k] = (int) $v;
        }

        $chartLabels = [];
        $chartDoanhThuCuoi = [];
        $chartDoanhThuThue = [];
        $chartSoHopDongCuoi = [];
        $chartSoHopDongThue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonthsNoOverflow($i)->startOfMonth();
            $chartLabels[] = $month->translatedFormat('m/Y');
            $startM = $month->copy()->startOfMonth();
            $endM = $month->copy()->endOfMonth();

            $chartDoanhThuCuoi[] = round((float) HopDongThanhToan::query()
                ->whereBetween('ngay_thanh_toan', [$startM->toDateString(), $endM->toDateString()])
                ->sum('so_tien'), 2);

            $chartDoanhThuThue[] = round((float) HopDongChoThueTrangPhuc::query()
                ->where('trang_thai', '!=', 2)
                ->whereBetween('created_at', [$startM, $endM])
                ->sum('tong_tien'), 2);

            $chartSoHopDongCuoi[] = (int) HopDongCuoi::query()
                ->whereBetween('created_at', [$startM, $endM])
                ->count();

            $chartSoHopDongThue[] = (int) HopDongChoThueTrangPhuc::query()
                ->whereBetween('created_at', [$startM, $endM])
                ->count();
        }

        $pieCuoiLabels = [];
        $pieCuoiSeries = [];
        foreach ($trangThaiCuoiTongQuanLabels as $ma => $nhan) {
            $pieCuoiLabels[] = $nhan;
            $pieCuoiSeries[] = (int) ($thongKeTrangThaiCuoi[$ma] ?? 0);
        }

        $pieThueLabels = [];
        $pieThueSeries = [];
        foreach ($trangThaiThueLabels as $ma => $nhan) {
            $pieThueLabels[] = $nhan;
            $pieThueSeries[] = (int) ($thongKeTrangThaiThue[$ma] ?? 0);
        }

        $topNhanVienSaleRows = DB::table('hop_dong_cuoi_thanh_vien_sale as t')
            ->join('hop_dong_cuoi as h', 'h.id', '=', 't.hop_dong_id')
            ->where('h.trang_thai_hop_dong', '!=', 'da_huy')
            ->selectRaw('t.nhan_vien_id, COUNT(DISTINCT t.hop_dong_id) as so_luong')
            ->groupBy('t.nhan_vien_id')
            ->orderByDesc('so_luong')
            ->limit(5)
            ->get();
        $nvSaleIds = $topNhanVienSaleRows->pluck('nhan_vien_id')->filter()->all();
        $nvSaleById = $nvSaleIds === []
            ? collect()
            : NhanVien::query()->with('user')->whereIn('id', $nvSaleIds)->get()->keyBy('id');
        $topNhanVienSale = $topNhanVienSaleRows->map(function ($row) use ($nvSaleById) {
            $nv = $nvSaleById->get($row->nhan_vien_id);

            return [
                'ten' => $nv?->user?->name ?? ('Nhân viên #'.$row->nhan_vien_id),
                'so_luong' => (int) $row->so_luong,
            ];
        })->all();

        $topDichVuRows = collect(DB::select('
            SELECT dich_vu_le_id, COUNT(DISTINCT hop_dong_cuoi_id) AS so_luong
            FROM (
                SELECT d.hop_dong_cuoi_id, d.dich_vu_le_id
                FROM hop_dong_cuoi_dich_vu_le d
                INNER JOIN hop_dong_cuoi h ON h.id = d.hop_dong_cuoi_id AND h.trang_thai_hop_dong != ?
                UNION
                SELECT n.hop_dong_cuoi_id, n.dich_vu_le_id
                FROM hop_dong_cuoi_nhom_dich_vu n
                INNER JOIN hop_dong_cuoi h ON h.id = n.hop_dong_cuoi_id AND h.trang_thai_hop_dong != ?
                WHERE n.trang_thai_su_dung = 1
            ) x
            GROUP BY dich_vu_le_id
            ORDER BY so_luong DESC
            LIMIT 5
        ', ['da_huy', 'da_huy']));
        $dichVuIds = $topDichVuRows->pluck('dich_vu_le_id')->filter()->all();
        $dichVuById = $dichVuIds === []
            ? collect()
            : DichVuLe::query()->whereIn('id', $dichVuIds)->get()->keyBy('id');
        $topDichVu = $topDichVuRows->map(function ($row) use ($dichVuById) {
            $dv = $dichVuById->get($row->dich_vu_le_id);

            return [
                'ten' => $dv?->ten_dich_vu ?? ('Dịch vụ #'.$row->dich_vu_le_id),
                'so_luong' => (int) $row->so_luong,
            ];
        })->all();

        $topTrangPhucRows = DB::table('hop_dong_cuoi_trang_phuc as p')
            ->join('hop_dong_cuoi as h', 'h.id', '=', 'p.hop_dong_cuoi_id')
            ->where('h.trang_thai_hop_dong', '!=', 'da_huy')
            ->selectRaw('p.trang_phuc_id, COUNT(*) as so_luong')
            ->groupBy('p.trang_phuc_id')
            ->orderByDesc('so_luong')
            ->limit(5)
            ->get();
        $tpIds = $topTrangPhucRows->pluck('trang_phuc_id')->filter()->all();
        $tpById = $tpIds === []
            ? collect()
            : TrangPhuc::query()->whereIn('id', $tpIds)->get()->keyBy('id');
        $topTrangPhucThue = $topTrangPhucRows->map(function ($row) use ($tpById) {
            $tp = $tpById->get($row->trang_phuc_id);

            return [
                'ten' => $tp?->ten_san_pham ?? ('Trang phục #'.$row->trang_phuc_id),
                'so_luong' => (int) $row->so_luong,
            ];
        })->all();

        $topConceptRows = HopDongCuoi::query()
            ->whereNotNull('concept_id')
            ->where('trang_thai_hop_dong', '!=', 'da_huy')
            ->selectRaw('concept_id, COUNT(*) as so_luong')
            ->groupBy('concept_id')
            ->orderByDesc('so_luong')
            ->limit(5)
            ->get();
        $conceptIds = $topConceptRows->pluck('concept_id')->filter()->all();
        $conceptById = $conceptIds === []
            ? collect()
            : Concept::query()->whereIn('id', $conceptIds)->get()->keyBy('id');
        $topConcept = $topConceptRows->map(function ($row) use ($conceptById) {
            $c = $conceptById->get($row->concept_id);

            return [
                'ten' => $c?->ten_concept ?? ('Concept #'.$row->concept_id),
                'so_luong' => (int) $row->so_luong,
            ];
        })->all();

        $view = view('admin.index', compact(
            'tuNgay',
            'denNgay',
            'soHopDongCuoi',
            'soHopDongThueTrangPhuc',
            'soKhachHang',
            'soNhanVien',
            'soTrangPhuc',
            'thongKeNhanVienTheoPhongBan',
            'soNhanVienChuaGanPhongBan',
            'doanhThuCuoiTrongKy',
            'doanhThuThueTrongKy',
            'tongGiaTriHopDongCuoi',
            'tongDaThuCuoi',
            'trangThaiCuoiTongQuanLabels',
            'thongKeTrangThaiCuoi',
            'trangThaiThueLabels',
            'thongKeTrangThaiThue',
            'chartLabels',
            'chartDoanhThuCuoi',
            'chartDoanhThuThue',
            'chartSoHopDongCuoi',
            'chartSoHopDongThue',
            'pieCuoiLabels',
            'pieCuoiSeries',
            'pieThueLabels',
            'pieThueSeries',
            'topNhanVienSale',
            'topDichVu',
            'topTrangPhucThue',
            'topConcept',
        ));

        return $validator->fails() ? $view->withErrors($validator) : $view;
    }

    public function thongTinCaNhan()
    {
        $user = auth()->user()->load('nhanVien');
        $nhanVien = $user->nhanVien;

        return view('admin.thong-tin-ca-nhan', compact('user', 'nhanVien'));
    }

    public function capNhatThongTinCaNhan(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
        ];

        $nhanVien = $user->nhanVien;
        if ($nhanVien) {
            $rules['gioi_tinh'] = ['nullable', 'string', 'in:nam,nu,khac'];
            $rules['ngay_sinh'] = ['nullable', 'date'];
            $rules['cccd'] = ['nullable', 'string', 'max:20'];
            $rules['hinh_anh'] = ['nullable', 'image', 'max:2048'];
        }

        $validated = $request->validate($rules);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($nhanVien) {
            $data = [
                'gioi_tinh' => $validated['gioi_tinh'] ?? null,
                'ngay_sinh' => $validated['ngay_sinh'] ?? null,
                'cccd' => $validated['cccd'] ?? null,
            ];
            if ($request->hasFile('hinh_anh')) {
                if ($nhanVien->hinh_anh) {
                    Storage::disk('public')->delete($nhanVien->hinh_anh);
                }
                $data['hinh_anh'] = $request->file('hinh_anh')->store('nhan-vien', 'public');
            }
            $nhanVien->update($data);
        }

        return redirect()->route('admin.thong-tin-ca-nhan')->with('success', 'Đã cập nhật thông tin cá nhân.');
    }

    public function doiMatKhau(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'password_current' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (! Hash::check($value, $user->password)) {
                        $fail('Mật khẩu hiện tại không đúng.');
                    }
                },
            ],
            'password_new' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'password_new.required' => 'Vui lòng nhập mật khẩu mới.',
            'password_new.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'password_new.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
        ]);

        $user->update([
            'password' => Hash::make($request->password_new),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Đã đổi mật khẩu thành công.']);
        }

        return redirect()->route('admin.thong-tin-ca-nhan')->with('success_password', 'Đã đổi mật khẩu thành công.');
    }
}

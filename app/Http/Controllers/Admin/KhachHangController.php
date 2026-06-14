<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concept;
use App\Models\DichVuLe;
use App\Models\HopDongCuoi;
use App\Models\HopDongCuoiDichVuLe;
use App\Models\HopDongCuoiNhomDichVu;
use App\Models\HopDongCuoiTrangPhuc;
use App\Models\HopDongThanhToan;
use App\Models\NhanVien;
use App\Models\NhomDichVu;
use App\Models\ThanhVienHopDongCuoi;
use App\Models\TrangPhuc;
use App\Support\AdminPagination;
use App\Support\HopDongCuoiLocTienDoFilter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KhachHangController extends Controller
{
    public function danhSachHopDongCuoi(Request $request)
    {
        $validated = $request->validate([
            'tu_khoa' => 'nullable|string|max:200',
            'ngay_cuoi_tu' => 'nullable|date',
            'ngay_cuoi_den' => 'nullable|date',
            'loai_hop_dong' => 'nullable|string|in:'.implode(',', array_keys(HopDongCuoi::LOAI_HOP_DONG)),
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(HopDongCuoi::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = HopDongCuoi::query()
            ->with(['concept', 'nhomDichVu', 'thoChup.user', 'thoMake.user', 'thoEdit.user'])
            ->whereRaw('LENGTH(TRIM(COALESCE(ten_co_dau, ?))) > 0', [''])
            ->whereRaw('LENGTH(TRIM(COALESCE(ten_chu_re, ?))) > 0', [''])
            ->where('trang_thai_hop_dong', '!=', 'nhap');

        $tuKhoa = trim((string) ($validated['tu_khoa'] ?? ''));
        if ($tuKhoa !== '') {
            $like = '%'.addcslashes($tuKhoa, '%_\\').'%';
            $query->where(function ($q) use ($like) {
                $q->where('ma_hop_dong', 'like', $like)
                    ->orWhere('ten_co_dau', 'like', $like)
                    ->orWhere('ten_chu_re', 'like', $like)
                    ->orWhere('email_sdt_co_dau', 'like', $like)
                    ->orWhere('email_sdt_chu_re', 'like', $like);
            });
        }

        $loaiHopDong = trim((string) ($validated['loai_hop_dong'] ?? ''));
        if ($loaiHopDong !== '') {
            $query->where('loai_hop_dong', $loaiHopDong);
        }

        $locFilters = HopDongCuoiLocTienDoFilter::parseFromRequest($request);
        HopDongCuoiLocTienDoFilter::apply($query, $locFilters);

        $ngayTu = $validated['ngay_cuoi_tu'] ?? null;
        $ngayDen = $validated['ngay_cuoi_den'] ?? null;
        if ($ngayTu || $ngayDen) {
            $from = $ngayTu ? Carbon::parse($ngayTu)->format('Y-m-d') : null;
            $to = $ngayDen ? Carbon::parse($ngayDen)->format('Y-m-d') : null;
            if ($from && $to && $from > $to) {
                [$from, $to] = [$to, $from];
            }
            $query->whereRaw('COALESCE(ngay_cuoi_chinh_thuc, ngay_cuoi_du_kien) IS NOT NULL');
            if ($from && $to) {
                $query->whereRaw(
                    'DATE(COALESCE(ngay_cuoi_chinh_thuc, ngay_cuoi_du_kien)) BETWEEN ? AND ?',
                    [$from, $to]
                );
            } elseif ($from) {
                $query->whereRaw(
                    'DATE(COALESCE(ngay_cuoi_chinh_thuc, ngay_cuoi_du_kien)) >= ?',
                    [$from]
                );
            } elseif ($to) {
                $query->whereRaw(
                    'DATE(COALESCE(ngay_cuoi_chinh_thuc, ngay_cuoi_du_kien)) <= ?',
                    [$to]
                );
            }
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? HopDongCuoi::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, HopDongCuoi::SAP_XEP_OPTIONS)) {
            $sapXepTheo = HopDongCuoi::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            HopDongCuoi::SAP_XEP_TEN_CO_DAU => $query->orderBy('ten_co_dau', $thuTu),
            HopDongCuoi::SAP_XEP_TEN_CHU_RE => $query->orderBy('ten_chu_re', $thuTu),
            HopDongCuoi::SAP_XEP_NGAY_CUOI => $query->orderByRaw('COALESCE(ngay_cuoi_chinh_thuc, ngay_cuoi_du_kien) '.$thuTu),
            HopDongCuoi::SAP_XEP_NGAY_CHUP => $query->orderByRaw('COALESCE(ngay_chup_thuc_te, ngay_chup_du_kien) '.$thuTu),
            HopDongCuoi::SAP_XEP_TIEN_DO_THANH_TOAN => $query->orderByRaw(
                'CASE WHEN tong_tien > 0 THEN tien_coc / tong_tien ELSE 0 END '.$thuTu
            ),
            HopDongCuoi::SAP_XEP_MA_HOP_DONG => $query->orderBy('ma_hop_dong', $thuTu),
            HopDongCuoi::SAP_XEP_TONG_TIEN => $query->orderBy('tong_tien', $thuTu),
            HopDongCuoi::SAP_XEP_CREATED_AT => $query->orderBy('created_at', $thuTu),
            default => $query->orderBy('id', $thuTu),
        };

        $hopDongCuois = $query->paginate(AdminPagination::perPage())->withQueryString();

        $danhSachNhanVien = NhanVien::query()
            ->with('user')
            ->orderBy('id')
            ->get();

        $locTienDoFilters = HopDongCuoiLocTienDoFilter::options();

        return view('admin.khach-hang.danh-sach-hop-dong-cuoi', compact(
            'hopDongCuois',
            'danhSachNhanVien',
            'locTienDoFilters',
            'locFilters',
        ));
    }

    /**
     * Dữ liệu thanh toán (tổng / đã trả / còn lại + lịch sử) cho modal danh sách HĐ cưới.
     */
    public function thongTinThanhToanHopDongCuoi(HopDongCuoi $hopDongCuoi): JsonResponse
    {
        $phaiThu = $hopDongCuoi->tongPhaiThu();
        $daThanhToan = $hopDongCuoi->tongDaThanhToan();
        $conLai = $hopDongCuoi->soTienConLai();

        $lichSu = HopDongThanhToan::query()
            ->where('hop_dong_id', $hopDongCuoi->id)
            ->orderByDesc('ngay_thanh_toan')
            ->orderByDesc('id')
            ->get()
            ->map(static function (HopDongThanhToan $r) {
                $paths = array_values(array_filter((array) ($r->proof_urls ?? [])));

                return [
                    'id' => $r->id,
                    'lan_thanh_toan' => $r->lan_thanh_toan,
                    'so_tien' => (float) $r->so_tien,
                    'ngay_thanh_toan' => $r->ngay_thanh_toan?->format('Y-m-d'),
                    'hinh_thuc_thanh_toan' => $r->hinh_thuc_thanh_toan,
                    'ghi_chu' => $r->ghi_chu,
                    'proof_urls' => $paths,
                    'proof_urls_public' => array_map(
                        static fn (string $p) => asset('storage/'.$p),
                        $paths
                    ),
                ];
            });

        return response()->json([
            'hop_dong_cuoi_id' => $hopDongCuoi->id,
            'ma_hop_dong' => $hopDongCuoi->ma_hop_dong,
            'phai_thu' => $phaiThu,
            'da_thanh_toan' => $daThanhToan,
            'con_lai' => $conLai,
            'lich_su' => $lichSu,
        ]);
    }

    /**
     * Ghi nhận một lần thanh toán (chuyển khoản / tiền mặt).
     */
    public function luuThanhToanHopDongCuoi(Request $request, HopDongCuoi $hopDongCuoi)
    {
        $conLai = $hopDongCuoi->soTienConLai();

        $validated = $request->validate([
            'so_tien' => 'required|numeric|min:0.01',
            'hinh_thuc_thanh_toan' => 'required|in:'.HopDongThanhToan::HINH_THUC_CHUYEN_KHOAN.','.HopDongThanhToan::HINH_THUC_TIEN_MAT,
            'ghi_chu' => 'nullable|string|max:2000',
            'proof_images' => 'nullable|array',
            'proof_images.*' => 'image|max:5120',
        ], [], [
            'so_tien' => 'số tiền',
            'hinh_thuc_thanh_toan' => 'hình thức thanh toán',
            'ghi_chu' => 'ghi chú',
            'proof_images' => 'ảnh chứng từ',
            'proof_images.*' => 'ảnh chứng từ',
        ]);

        $soTien = (float) $validated['so_tien'];
        if ($soTien - $conLai > 0.00001) {
            throw ValidationException::withMessages([
                'so_tien' => 'Số tiền không được vượt quá số còn lại cần thanh toán ('.number_format($conLai, 0, ',', '.').' đ).',
            ]);
        }

        $lanMoi = (int) HopDongThanhToan::query()
            ->where('hop_dong_id', $hopDongCuoi->id)
            ->max('lan_thanh_toan');
        $lanMoi++;

        $dir = 'hop-dong-thanh-toan/'.$hopDongCuoi->id;

        DB::transaction(function () use ($hopDongCuoi, $validated, $soTien, $lanMoi, $request, $dir) {
            $proofPaths = [];
            foreach ($request->file('proof_images', []) as $file) {
                if ($file === null || ! $file->isValid()) {
                    continue;
                }
                $proofPaths[] = $file->store($dir, 'public');
            }

            HopDongThanhToan::query()->create([
                'hop_dong_id' => $hopDongCuoi->id,
                'lan_thanh_toan' => $lanMoi,
                'so_tien' => $soTien,
                'ngay_thanh_toan' => Carbon::today()->toDateString(),
                'hinh_thuc_thanh_toan' => $validated['hinh_thuc_thanh_toan'],
                'proof_urls' => $proofPaths !== [] ? $proofPaths : null,
                'ghi_chu' => $validated['ghi_chu'] ?? null,
                'created_by' => $request->user()?->nhanVien?->id,
            ]);
            $hopDongCuoi->increment('tien_coc', $soTien);
        });

        if ($request->wantsJson()) {
            return $this->thongTinThanhToanHopDongCuoi($hopDongCuoi->fresh());
        }

        return redirect()
            ->route('admin.khach-hang.danh-sach-hop-dong-cuoi')
            ->with('success', 'Đã ghi nhận thanh toán.');
    }

    /**
     * Cập nhật điều phối (lịch, link, ekip, ghi chú sale) từ modal danh sách hợp đồng cưới.
     */
    public function capNhatDieuPhoiHopDongCuoi(Request $request, HopDongCuoi $hopDongCuoi)
    {
        $validated = $request->validate([
            'buoi_chup' => 'nullable|in:sang,chieu,ca_ngay',
            'ngay_chup_thuc_te' => 'nullable|date',
            'gio_chup' => 'nullable|date_format:H:i',
            'ngay_cuoi_chinh_thuc' => 'nullable|date',
            'dia_diem_chup' => 'nullable|string',
            'ngay_tra_link_demo_chinh_thuc' => 'nullable|date',
            'ngay_tra_link_in_chinh_thuc' => 'nullable|date',
            'tho_chup_id' => 'nullable|exists:nhan_vien,id',
            'tho_make_id' => 'nullable|exists:nhan_vien,id',
            'tho_edit_id' => 'nullable|exists:nhan_vien,id',
            'ghi_chu_sale' => 'nullable|string',
        ], [], [
            'buoi_chup' => 'buổi chụp',
            'ngay_chup_thuc_te' => 'ngày chụp chính thức',
            'gio_chup' => 'giờ chụp',
            'ngay_cuoi_chinh_thuc' => 'ngày cưới chính thức',
            'dia_diem_chup' => 'địa điểm chụp',
            'ngay_tra_link_demo_chinh_thuc' => 'ngày trả link demo chính thức',
            'ngay_tra_link_in_chinh_thuc' => 'ngày trả link in chính thức',
            'tho_chup_id' => 'người chụp',
            'tho_make_id' => 'người make',
            'tho_edit_id' => 'người edit',
            'ghi_chu_sale' => 'ghi chú',
        ]);

        $ngayChup = $validated['ngay_chup_thuc_te'] ?? null;
        if ($ngayChup) {
            $banIds = $this->layTapIdNhanVienBanChoNgayChupThucTe(
                Carbon::parse($ngayChup)->toDateString(),
                $hopDongCuoi->id
            );
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
        }

        if (! empty($validated['gio_chup'])) {
            $validated['gio_chup'] .= ':00';
        }

        $hopDongCuoi->fill($validated);
        $hopDongCuoi->save();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Đã cập nhật điều phối hợp đồng.']);
        }

        return back()->with('success', 'Đã cập nhật điều phối hợp đồng.');
    }

    /**
     * JSON: danh sách nhân viên cho modal điều phối; disabled nếu đã là thợ chụp hoặc thợ make
     * của hợp đồng khác có cùng ngày chụp thực tế.
     */
    public function nhanVienChoDieuPhoiTheoNgayChup(Request $request, HopDongCuoi $hopDongCuoi): JsonResponse
    {
        $validated = $request->validate([
            'ngay' => 'required|date',
        ], [], [
            'ngay' => 'ngày chụp',
        ]);

        $ngay = Carbon::parse($validated['ngay'])->toDateString();
        $banIds = $this->layTapIdNhanVienBanChoNgayChupThucTe($ngay, $hopDongCuoi->id);

        $items = NhanVien::query()
            ->with('user')
            ->orderBy('id')
            ->get()
            ->map(function (NhanVien $nv) use ($banIds): array {
                $id = (int) $nv->id;

                return [
                    'id' => $id,
                    'ten' => $nv->user?->name ?? ('Nhân viên #'.$id),
                    'disabled' => isset($banIds[$id]),
                ];
            });

        return response()->json(['items' => $items]);
    }

    /**
     * @return array<int, true> id nhân viên đã được gán chụp hoặc make ở HĐ khác trong cùng ngày chụp thực tế
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

    public function taoHopDong(Request $request)
    {
        $maParam = trim((string) $request->query('ma_hop_dong', ''));

        if ($maParam !== '') {
            $hopDongCuoi = HopDongCuoi::query()->where('ma_hop_dong', $maParam)->first();
            if (! $hopDongCuoi) {
                return redirect()->route('admin.khach-hang.tao-hop-dong-canh-bao', [
                    'ma_hop_dong' => $maParam,
                ]);
            }

            return $this->hienThiWizardHopDongCuoi($hopDongCuoi, false);
        }

        $hopDongCuoi = $this->taoBanGhiHopDongCuoiNhapMoi();

        return redirect()->route('admin.khach-hang.tao-hop-dong', [
            'ma_hop_dong' => $hopDongCuoi->ma_hop_dong,
        ]);
    }

    /**
     * Cùng giao diện wizard với tạo hợp đồng, URL riêng để mở từ danh sách.
     */
    public function chinhSuaHopDongCuoi(HopDongCuoi $hopDongCuoi)
    {
        return $this->hienThiWizardHopDongCuoi($hopDongCuoi, true);
    }

    public function huyHopDongCuoi(Request $request, HopDongCuoi $hopDongCuoi)
    {
        if ($hopDongCuoi->trang_thai_hop_dong !== 'da_huy') {
            $hopDongCuoi->forceFill([
                'trang_thai_hop_dong' => 'da_huy',
            ])->save();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Đã huỷ hợp đồng.',
                'hop_dong_cuoi_id' => $hopDongCuoi->id,
                'trang_thai_hop_dong' => $hopDongCuoi->trang_thai_hop_dong,
            ]);
        }

        return redirect()
            ->route('admin.khach-hang.danh-sach-hop-dong-cuoi')
            ->with('success', 'Đã huỷ hợp đồng.');
    }

    private function hienThiWizardHopDongCuoi(HopDongCuoi $hopDongCuoi, bool $laManChinhSuaHopDong)
    {
        $hopDongCuoi->load(['hopDongCuoiNhomDichVu', 'hopDongCuoiDichVuLe', 'hopDongCuoiTrangPhuc', 'thanhVienHopDongCuis']);
        $hopDongCuoiData = $this->layDuLieuHopDongChoForm($hopDongCuoi);
        $wizardStep2Restore = $this->buildWizardStep2RestorePayload($hopDongCuoi);

        $nhomDichVus = NhomDichVu::query()
            ->where('trang_thai', NhomDichVu::TRANG_THAI_HIEN_THI)
            ->with(['dichVuLe' => function ($query) {
                $query->select('dich_vu_le.id', 'ten_dich_vu', 'ma_dich_vu', 'gia_dich_vu', 'mo_ta')
                    ->orderBy('ten_dich_vu');
            }])
            ->withCount('dichVuLe')
            ->orderBy('ten_nhom')
            ->get();

        $dichVuLes = DichVuLe::query()
            ->where('trang_thai', DichVuLe::TRANG_THAI_HIEN_THI)
            ->orderBy('ten_dich_vu')
            ->get(['id', 'ten_dich_vu', 'ma_dich_vu', 'gia_dich_vu', 'mo_ta']);

        $concepts = Concept::query()
            ->where('trang_thai', Concept::TRANG_THAI_ACTIVE)
            ->orderBy('ten_concept')
            ->get(['id', 'ten_concept']);

        $trangPhucs = TrangPhuc::query()
            ->where('trang_thai', TrangPhuc::TRANG_THAI_ACTIVE)
            ->orderBy('ten_san_pham')
            ->get(['id', 'ten_san_pham', 'ma_san_pham']);

        $danhSachNhanVien = NhanVien::query()
            ->with('user')
            ->orderBy('id')
            ->get();

        return view('admin.khach-hang.tao-hop-dong', compact(
            'hopDongCuoi',
            'hopDongCuoiData',
            'wizardStep2Restore',
            'nhomDichVus',
            'dichVuLes',
            'concepts',
            'trangPhucs',
            'danhSachNhanVien',
            'laManChinhSuaHopDong'
        ));
    }

    public function taoHopDongCanhBao(Request $request)
    {
        $maHopDong = trim((string) $request->query('ma_hop_dong', ''));

        return view('admin.khach-hang.tao-hop-dong-canh-bao', compact('maHopDong'));
    }

    /**
     * Lưu thông tin bước 1 (thông tin khách) khi người dùng bấm Tiếp tục trên wizard tạo hợp đồng.
     */
    public function capNhatTaoHopDongBuoc1(Request $request, HopDongCuoi $hopDongCuoi)
    {
        $merge = $request->all();
        $loaiHopDong = trim((string) ($merge['loai_hop_dong'] ?? ''));
        $merge['loai_hop_dong'] = $loaiHopDong !== '' ? $loaiHopDong : null;
        foreach (['ngay_chup_du_kien', 'ngay_cuoi_du_kien'] as $k) {
            if (! array_key_exists($k, $merge)) {
                continue;
            }
            $v = $merge[$k];
            if ($v === '' || $v === null) {
                $merge[$k] = null;
            }
        }
        $request->merge($merge);

        $validated = $request->validate([
            'ten_co_dau' => 'required|string|max:150',
            'ten_chu_re' => 'required|string|max:150',
            'email_sdt_co_dau' => 'nullable|string|max:500',
            'email_sdt_chu_re' => 'nullable|string|max:500',
            'ngay_chup_du_kien' => 'nullable|date',
            'ngay_cuoi_du_kien' => 'nullable|date',
            'loai_hop_dong' => 'nullable|string|in:'.implode(',', array_keys(HopDongCuoi::LOAI_HOP_DONG)),
            'kenh_tiep_can' => 'nullable|string|max:100',
            'yeu_cau_dac_biet' => 'nullable|string',
            'thanh_vien_nhan_vien_ids' => 'nullable|array',
            'thanh_vien_nhan_vien_ids.*' => 'integer|exists:nhan_vien,id',
        ], [], [
            'ten_co_dau' => 'họ tên cô dâu',
            'ten_chu_re' => 'họ tên chú rể',
            'email_sdt_co_dau' => 'SĐT cô dâu',
            'email_sdt_chu_re' => 'SĐT chú rể',
            'ngay_chup_du_kien' => 'ngày chụp dự kiến',
            'ngay_cuoi_du_kien' => 'ngày cưới dự kiến',
            'loai_hop_dong' => 'loại hợp đồng',
            'kenh_tiep_can' => 'kênh tiếp cận',
            'yeu_cau_dac_biet' => 'yêu cầu đặc biệt',
            'thanh_vien_nhan_vien_ids' => 'thành viên sale',
        ]);

        $thanhVienIds = array_values(array_unique(array_map(
            static fn ($id) => (int) $id,
            $validated['thanh_vien_nhan_vien_ids'] ?? []
        )));
        $thanhVienIds = array_values(array_filter($thanhVienIds, static fn ($id) => $id > 0));
        unset($validated['thanh_vien_nhan_vien_ids']);

        DB::transaction(function () use ($hopDongCuoi, $validated, $thanhVienIds): void {
            $hopDongCuoi->fill($validated);
            $hopDongCuoi->save();

            ThanhVienHopDongCuoi::query()
                ->where('hop_dong_id', $hopDongCuoi->id)
                ->where('vai_tro', ThanhVienHopDongCuoi::VAI_TRO_THANH_VIEN)
                ->delete();

            foreach ($thanhVienIds as $nvId) {
                ThanhVienHopDongCuoi::query()->create([
                    'hop_dong_id' => $hopDongCuoi->id,
                    'nhan_vien_id' => $nvId,
                    'vai_tro' => ThanhVienHopDongCuoi::VAI_TRO_THANH_VIEN,
                ]);
            }
        });

        return response()->json([
            'message' => 'Đã lưu thông tin khách hàng.',
        ]);
    }

    /**
     * Lưu bước 2 (dịch vụ): loại hình, combo / ghép dịch vụ lẻ / combo nâng cấp và pivot tương ứng.
     */
    public function capNhatTaoHopDongBuoc2(Request $request, HopDongCuoi $hopDongCuoi)
    {
        $loai = $request->input('loai_dich_vu');

        $rules = [
            'loai_dich_vu' => 'required|in:combo_tron_goi,ghep_dich_vu_le,combo_va_nang_cap',
            'combo_goi' => [
                Rule::excludeIf($loai === 'ghep_dich_vu_le'),
                'nullable',
                'integer',
                Rule::requiredIf(in_array($loai, ['combo_tron_goi', 'combo_va_nang_cap'], true)),
                Rule::exists('nhom_dich_vu', 'id')->where('trang_thai', NhomDichVu::TRANG_THAI_HIEN_THI),
            ],
        ];

        if ($loai === 'ghep_dich_vu_le') {
            $rules['dich_vu_chon'] = 'required|array|min:1';
            $rules['dich_vu_chon.*.so_luong'] = 'required|integer|min:1';
        }

        if ($loai === 'combo_va_nang_cap') {
            $rules['dich_vu_trong_combo_nang_cap'] = 'nullable|array';
            $rules['dich_vu_trong_combo_nang_cap.*'] = 'integer|exists:dich_vu_le,id';
            $rules['dich_vu_nang_cap'] = 'nullable|array';
            $rules['dich_vu_nang_cap.*.so_luong'] = 'required|integer|min:1';
        }

        $validated = $request->validate($rules, [], [
            'loai_dich_vu' => 'hình thức dịch vụ',
            'combo_goi' => 'combo',
            'dich_vu_chon' => 'dịch vụ lẻ',
        ]);

        if ($validated['loai_dich_vu'] === 'ghep_dich_vu_le') {
            $ids = array_map('intval', array_keys($validated['dich_vu_chon']));
            $validCount = DichVuLe::query()
                ->whereIn('id', $ids)
                ->where('trang_thai', DichVuLe::TRANG_THAI_HIEN_THI)
                ->count();
            if ($validCount !== count($ids)) {
                throw ValidationException::withMessages([
                    'dich_vu_chon' => ['Một hoặc nhiều dịch vụ lẻ không hợp lệ hoặc đang ẩn.'],
                ]);
            }
        }

        if ($validated['loai_dich_vu'] === 'combo_va_nang_cap') {
            $nangCap = $validated['dich_vu_nang_cap'] ?? [];
            if ($nangCap !== []) {
                $idsNc = array_map('intval', array_keys($nangCap));
                $validNc = DichVuLe::query()
                    ->whereIn('id', $idsNc)
                    ->where('trang_thai', DichVuLe::TRANG_THAI_HIEN_THI)
                    ->count();
                if ($validNc !== count($idsNc)) {
                    throw ValidationException::withMessages([
                        'dich_vu_nang_cap' => ['Một hoặc nhiều dịch vụ nâng cấp không hợp lệ hoặc đang ẩn.'],
                    ]);
                }
            }

            $nhomId = (int) $validated['combo_goi'];
            $nhom = NhomDichVu::query()
                ->with(['dichVuLe' => static fn ($q) => $q->select('dich_vu_le.id')])
                ->findOrFail($nhomId);
            $dichVuIdsTrongNhom = $nhom->dichVuLe->pluck('id')->map(static fn ($id) => (int) $id)->all();
            $selectedTrongCombo = array_map('intval', $validated['dich_vu_trong_combo_nang_cap'] ?? []);
            foreach ($selectedTrongCombo as $dvId) {
                if (! in_array($dvId, $dichVuIdsTrongNhom, true)) {
                    throw ValidationException::withMessages([
                        'dich_vu_trong_combo_nang_cap' => ['Có dịch vụ không thuộc combo đã chọn.'],
                    ]);
                }
            }
        }

        $tongTien = $this->tinhTongTienDichVuBuoc2($validated);

        DB::transaction(function () use ($hopDongCuoi, $validated, $tongTien) {
            // Ghép lẻ: nhom_dich_vu_id = -1. Có combo (trọn gói hoặc nâng cấp): lưu id nhóm (nhom_dich_vu.id).
            $hopDongCuoi->fill([
                'loai_dich_vu' => $validated['loai_dich_vu'],
                'nhom_dich_vu_id' => $validated['loai_dich_vu'] === 'ghep_dich_vu_le'
                    ? -1
                    : (int) $validated['combo_goi'],
                'tong_tien' => $tongTien,
            ]);
            $hopDongCuoi->save();

            $hopDongCuoi->hopDongCuoiNhomDichVu()->delete();
            $hopDongCuoi->hopDongCuoiDichVuLe()->delete();

            if ($validated['loai_dich_vu'] === 'combo_tron_goi') {
                $nhomId = (int) $validated['combo_goi'];
                $nhom = NhomDichVu::query()
                    ->with(['dichVuLe' => static fn ($q) => $q->select('dich_vu_le.id')])
                    ->findOrFail($nhomId);
                foreach ($nhom->dichVuLe as $dv) {
                    HopDongCuoiNhomDichVu::query()->create([
                        'hop_dong_cuoi_id' => $hopDongCuoi->id,
                        'nhom_dich_vu_id' => $nhomId,
                        'dich_vu_le_id' => $dv->id,
                        'trang_thai_su_dung' => 1,
                    ]);
                }
            } elseif ($validated['loai_dich_vu'] === 'ghep_dich_vu_le') {
                foreach ($validated['dich_vu_chon'] as $dichVuLeId => $row) {
                    $qty = (int) ($row['so_luong'] ?? 1);
                    HopDongCuoiDichVuLe::query()->create([
                        'hop_dong_cuoi_id' => $hopDongCuoi->id,
                        'dich_vu_le_id' => (int) $dichVuLeId,
                        'so_luong' => max(1, $qty),
                    ]);
                }
            } elseif ($validated['loai_dich_vu'] === 'combo_va_nang_cap') {
                $nhomId = (int) $validated['combo_goi'];
                $nhom = NhomDichVu::query()
                    ->with(['dichVuLe' => static fn ($q) => $q->select('dich_vu_le.id')])
                    ->findOrFail($nhomId);
                $selectedSet = array_fill_keys(array_map('intval', $validated['dich_vu_trong_combo_nang_cap'] ?? []), true);
                foreach ($nhom->dichVuLe as $dv) {
                    HopDongCuoiNhomDichVu::query()->create([
                        'hop_dong_cuoi_id' => $hopDongCuoi->id,
                        'nhom_dich_vu_id' => $nhomId,
                        'dich_vu_le_id' => $dv->id,
                        'trang_thai_su_dung' => isset($selectedSet[(int) $dv->id]) ? 1 : 0,
                    ]);
                }
                foreach ($validated['dich_vu_nang_cap'] ?? [] as $dichVuLeId => $row) {
                    $qty = (int) ($row['so_luong'] ?? 1);
                    HopDongCuoiDichVuLe::query()->create([
                        'hop_dong_cuoi_id' => $hopDongCuoi->id,
                        'dich_vu_le_id' => (int) $dichVuLeId,
                        'so_luong' => max(1, $qty),
                    ]);
                }
            }
        });

        $hopDongCuoi->refresh();
        $hopDongCuoi->load(['hopDongCuoiNhomDichVu', 'hopDongCuoiDichVuLe']);

        return response()->json([
            'message' => 'Đã lưu dịch vụ và tổng tiền dịch vụ.',
            'tong_tien' => (float) $hopDongCuoi->tong_tien,
            'loai_dich_vu' => $hopDongCuoi->loai_dich_vu,
            'nhom_dich_vu_id' => (int) $hopDongCuoi->nhom_dich_vu_id,
            'wizard_step2' => $this->buildWizardStep2RestorePayload($hopDongCuoi),
        ]);
    }

    /**
     * Lưu bước 3 (thanh toán / xác nhận) từ wizard. Khi gửi kèm chinh_sua_hoan_tat, phản hồi JSON có URL chuyển về danh sách hợp đồng.
     */
    public function capNhatTaoHopDongBuoc3(Request $request, HopDongCuoi $hopDongCuoi)
    {
        $merge = $request->all();
        $cid = $merge['concept_id'] ?? null;
        if ($cid === '' || $cid === null) {
            $merge['concept_id'] = null;
        } else {
            $i = (int) $cid;
            $merge['concept_id'] = $i > 0 ? $i : null;
        }
        $tpRaw = $merge['trang_phuc'] ?? [];
        if (! is_array($tpRaw)) {
            $tpRaw = [];
        }
        $merge['trang_phuc'] = array_values(array_unique(array_map(
            static fn ($v) => (int) $v,
            array_filter($tpRaw, static fn ($v) => $v !== null && $v !== '')
        )));
        foreach (['han_thanh_toan_lan2', 'han_thanh_toan_lan3', 'ngay_ky_hop_dong'] as $k) {
            if (! array_key_exists($k, $merge)) {
                continue;
            }
            $v = $merge[$k];
            if ($v === '' || $v === null) {
                $merge[$k] = null;
            }
        }
        foreach (['tong_tien', 'chiet_khau', 'tien_coc'] as $k) {
            if (! array_key_exists($k, $merge)) {
                continue;
            }
            if ($merge[$k] === '' || $merge[$k] === null) {
                $merge[$k] = 0;
            }
        }
        $request->merge($merge);

        $validated = $request->validate([
            'concept_id' => 'nullable|integer|exists:concept,id',
            'trang_phuc' => 'nullable|array',
            'trang_phuc.*' => 'integer|exists:trang_phuc,id',
            'tong_tien' => 'required|numeric|min:0',
            'chiet_khau' => 'nullable|numeric|min:0',
            'tien_coc' => 'nullable|numeric|min:0',
            'hinh_thuc_coc' => 'required|in:'.HopDongCuoi::HINH_THUC_COC_TAI_CUA_HANG.','.HopDongCuoi::HINH_THUC_COC_ONLINE,
            'han_thanh_toan_lan2' => 'nullable|date',
            'han_thanh_toan_lan3' => 'nullable|date',
            'ngay_ky_hop_dong' => 'nullable|date',
            'dong_y' => 'accepted',
            'chinh_sua_hoan_tat' => 'nullable|boolean',
        ], [], [
            'concept_id' => 'concept',
            'trang_phuc' => 'trang phục',
            'tong_tien' => 'tổng tiền',
            'chiet_khau' => 'chiết khấu',
            'tien_coc' => 'tiền cọc',
            'hinh_thuc_coc' => 'hình thức cọc',
            'han_thanh_toan_lan2' => 'hạn thanh toán lần 2',
            'han_thanh_toan_lan3' => 'hạn thanh toán lần 3',
            'ngay_ky_hop_dong' => 'ngày ký hợp đồng',
            'dong_y' => 'xác nhận thông tin',
        ]);

        $tpIdsValidated = array_values(array_unique(array_map('intval', $validated['trang_phuc'] ?? [])));
        if ($tpIdsValidated !== []) {
            $tpActiveCount = TrangPhuc::query()
                ->whereIn('id', $tpIdsValidated)
                ->where('trang_thai', TrangPhuc::TRANG_THAI_ACTIVE)
                ->count();
            if ($tpActiveCount !== count($tpIdsValidated)) {
                throw ValidationException::withMessages([
                    'trang_phuc' => ['Một hoặc nhiều trang phục không hợp lệ hoặc đang ẩn.'],
                ]);
            }
        }

        $payload = [
            'concept_id' => $validated['concept_id'] ?? null,
            'tong_tien' => $validated['tong_tien'],
            'chiet_khau' => $validated['chiet_khau'] ?? 0,
            'tien_coc' => $validated['tien_coc'] ?? 0,
            'hinh_thuc_coc' => $validated['hinh_thuc_coc'],
            'han_thanh_toan_lan2' => $validated['han_thanh_toan_lan2'] ?? null,
            'han_thanh_toan_lan3' => $validated['han_thanh_toan_lan3'] ?? null,
            'ngay_ky_hop_dong' => $validated['ngay_ky_hop_dong'] ?? null,
        ];
        if ($hopDongCuoi->trang_thai_hop_dong === 'nhap') {
            $payload['trang_thai_hop_dong'] = 'dang_thuc_hien';
        }

        DB::transaction(function () use ($hopDongCuoi, $payload, $tpIdsValidated): void {
            $hopDongCuoi->fill($payload);
            $hopDongCuoi->save();

            $hopDongCuoi->hopDongCuoiTrangPhuc()->delete();
            foreach ($tpIdsValidated as $tpId) {
                if ($tpId <= 0) {
                    continue;
                }
                HopDongCuoiTrangPhuc::query()->create([
                    'hop_dong_cuoi_id' => $hopDongCuoi->id,
                    'trang_phuc_id' => $tpId,
                ]);
            }
        });

        $message = 'Đã lưu concept, trang phục và thông tin thanh toán.';
        $payload = ['message' => $message];

        if ($request->boolean('chinh_sua_hoan_tat')) {
            $request->session()->flash('success', $message);
            $payload['redirect'] = route('admin.khach-hang.danh-sach-hop-dong-cuoi');
        }

        return response()->json($payload);
    }

    public function storeHopDongCuoi(Request $request)
    {
        $merge = $request->all();
        $merge['ma_hop_dong'] = trim((string) ($merge['ma_hop_dong'] ?? '')) ?: null;
        foreach (['concept_id', 'nguoi_up_link_demo_id', 'nguoi_up_link_in_id'] as $k) {
            $v = $merge[$k] ?? null;
            if ($v === '' || $v === null) {
                $merge[$k] = null;
            } else {
                $i = (int) $v;
                $merge[$k] = $i > 0 ? $i : null;
            }
        }
        $request->merge($merge);

        $validated = $request->validate([
            'ma_hop_dong' => 'nullable|string|max:30|unique:hop_dong_cuoi,ma_hop_dong',
            'ten_co_dau' => 'required|string|max:150',
            'ten_chu_re' => 'required|string|max:150',
            'email_sdt_co_dau' => 'nullable|string',
            'email_sdt_chu_re' => 'nullable|string',
            'ngay_chup_du_kien' => 'nullable|date',
            'ngay_chup_thuc_te' => 'nullable|date',
            'buoi_chup' => 'nullable|in:sang,chieu,ca_ngay',
            'ngay_cuoi_du_kien' => 'nullable|date',
            'ngay_cuoi_chinh_thuc' => 'nullable|date',
            'dia_diem_chup' => 'nullable|string',
            'concept_id' => 'nullable|integer|exists:concept,id',
            'kenh_tiep_can' => 'nullable|string|max:100',
            'yeu_cau_dac_biet' => 'nullable|string',
            'tong_tien' => 'nullable|numeric|min:0',
            'chiet_khau' => 'nullable|numeric|min:0',
            'tien_coc' => 'nullable|numeric|min:0',
            'trang_thai_hop_dong' => ['nullable', Rule::in(HopDongCuoi::TRANG_THAI_HOP_DONG)],
            'link_demo' => 'nullable|string|max:500',
            'ngay_tra_link_demo_du_kien' => 'nullable|date',
            'ngay_up_link_demo_gan_nhat' => 'nullable|date',
            'nguoi_up_link_demo_id' => 'nullable|integer|exists:nhan_vien,id',
            'link_in' => 'nullable|string|max:500',
            'ngay_tra_link_in_chinh_thuc' => 'nullable|date',
            'ngay_up_link_in_gan_nhat' => 'nullable|date',
            'nguoi_up_link_in_id' => 'nullable|integer|exists:nhan_vien,id',
            'ghi_chu_sale' => 'nullable|string',
            'ngay_ky_hop_dong' => 'nullable|date',
            'han_thanh_toan_lan2' => 'nullable|date',
            'han_thanh_toan_lan3' => 'nullable|date',
        ]);

        $validated['created_by'] = $request->user()?->nhanVien?->id;

        $hop = HopDongCuoi::create($validated);

        if ($hop->wasRecentlyCreated && empty($hop->ma_hop_dong)) {
            $hop->forceFill([
                'ma_hop_dong' => now()->format('dmy').$hop->id,
            ])->save();
        }

        return redirect()
            ->route('admin.khach-hang.tao-hop-dong', ['ma_hop_dong' => $hop->ma_hop_dong])
            ->with('success', 'Đã tạo hợp đồng cưới.');
    }

    /**
     * Kiểm tra mã giảm giá theo mã hợp đồng cưới đã hoàn thành.
     * Rule:
     * - Tổng tiền > 300.000 => giảm 300.000
     * - Tổng tiền <= 300.000 => giảm 50% tổng tiền
     */
    public function kiemTraMaGiamGiaTaoHopDong(Request $request)
    {
        $validated = $request->validate([
            'ma_giam_gia' => 'required|string|max:30',
            'tong_tien' => 'required|numeric|min:0',
        ], [], [
            'ma_giam_gia' => 'mã giảm giá',
            'tong_tien' => 'tổng tiền',
        ]);

        $maGiamGia = trim((string) $validated['ma_giam_gia']);
        $tongTien = (float) $validated['tong_tien'];

        $isValid = HopDongCuoi::query()
            ->where('ma_hop_dong', $maGiamGia)
            ->whereNotIn('trang_thai_hop_dong', ['nhap', 'da_huy'])
            ->exists();

        if (! $isValid) {
            return response()->json([
                'valid' => false,
                'so_tien_giam_gia' => 0,
                'message' => 'Mã giảm giá không hợp lệ.',
            ]);
        }

        $soTienGiamGia = $tongTien > 300000 ? 300000 : round($tongTien * 0.5, 2);

        return response()->json([
            'valid' => true,
            'so_tien_giam_gia' => $soTienGiamGia,
            'message' => 'Mã giảm giá hợp lệ.',
        ]);
    }

    private function taoBanGhiHopDongCuoiNhapMoi(): HopDongCuoi
    {
        return DB::transaction(function () {
            $hop = HopDongCuoi::query()->create([
                'ma_hop_dong' => 'T'.bin2hex(random_bytes(8)),
                'ten_co_dau' => '',
                'ten_chu_re' => '',
                'created_by' => request()->user()?->nhanVien?->id,
            ]);

            $hop->forceFill([
                'ma_hop_dong' => now()->format('dmy').$hop->id,
            ])->save();

            $hop = $hop->fresh();
            $nguoiTaoNvId = request()->user()?->nhanVien?->id;
            if ($nguoiTaoNvId) {
                ThanhVienHopDongCuoi::query()->create([
                    'hop_dong_id' => $hop->id,
                    'nhan_vien_id' => (int) $nguoiTaoNvId,
                    'vai_tro' => ThanhVienHopDongCuoi::VAI_TRO_NGUOI_TAO,
                ]);
            }

            return $hop;
        });
    }

    /**
     * Tổng tiền dịch vụ (khớp cách tính hiển thị trên wizard bước 2).
     */
    private function tinhTongTienDichVuBuoc2(array $v): float
    {
        $loai = $v['loai_dich_vu'];
        if ($loai === 'combo_tron_goi') {
            $nhom = NhomDichVu::query()->find((int) $v['combo_goi']);

            return round((float) ($nhom?->gia_tien ?? 0), 2);
        }
        if ($loai === 'ghep_dich_vu_le') {
            $ids = array_map('intval', array_keys($v['dich_vu_chon']));
            $prices = DichVuLe::query()
                ->whereIn('id', $ids)
                ->pluck('gia_dich_vu', 'id');
            $sum = 0.0;
            foreach ($v['dich_vu_chon'] as $idKey => $row) {
                $id = (int) $idKey;
                $qty = max(1, (int) ($row['so_luong'] ?? 1));
                $gia = (float) ($prices[$id] ?? 0);
                $sum += $gia * $qty;
            }

            return round($sum, 2);
        }

        $nhom = NhomDichVu::query()->find((int) $v['combo_goi']);
        $comboGia = round((float) ($nhom?->gia_tien ?? 0), 2);
        $sumNc = 0.0;
        foreach ($v['dich_vu_nang_cap'] ?? [] as $idKey => $row) {
            $id = (int) $idKey;
            $qty = max(1, (int) ($row['so_luong'] ?? 1));
            $gia = (float) (DichVuLe::query()->where('id', $id)->value('gia_dich_vu') ?? 0);
            $sumNc += $gia * $qty;
        }

        return round($comboGia + $sumNc, 2);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildWizardStep2RestorePayload(HopDongCuoi $hop): ?array
    {
        if (empty($hop->loai_dich_vu)) {
            return null;
        }
        $payload = [
            'loai_dich_vu' => $hop->loai_dich_vu,
            'nhom_dich_vu_id' => (int) $hop->nhom_dich_vu_id > 0 ? (int) $hop->nhom_dich_vu_id : null,
        ];
        if ($hop->loai_dich_vu === 'ghep_dich_vu_le') {
            $payload['dich_vu_le'] = $hop->hopDongCuoiDichVuLe
                ->map(static fn (HopDongCuoiDichVuLe $r) => [
                    'id' => (int) $r->dich_vu_le_id,
                    'so_luong' => (int) $r->so_luong,
                ])
                ->values()
                ->all();
        }
        if ($hop->loai_dich_vu === 'combo_va_nang_cap') {
            $payload['combo_dich_vu_checked_ids'] = $hop->hopDongCuoiNhomDichVu
                ->where('trang_thai_su_dung', 1)
                ->pluck('dich_vu_le_id')
                ->map(static fn ($id) => (int) $id)
                ->values()
                ->all();
            $payload['dich_vu_nang_cap'] = $hop->hopDongCuoiDichVuLe
                ->map(static fn (HopDongCuoiDichVuLe $r) => [
                    'id' => (int) $r->dich_vu_le_id,
                    'so_luong' => (int) $r->so_luong,
                ])
                ->values()
                ->all();
        }

        return $payload;
    }

    private function layDuLieuHopDongChoForm(HopDongCuoi $hopDongCuoi): array
    {
        return [
            'ma_hop_dong' => $hopDongCuoi->ma_hop_dong,
            'loai_hop_dong' => $hopDongCuoi->loai_hop_dong,
            'ten_chu_re' => $hopDongCuoi->ten_chu_re,
            'ten_co_dau' => $hopDongCuoi->ten_co_dau,
            'email_sdt_chu_re' => $hopDongCuoi->email_sdt_chu_re,
            'email_sdt_co_dau' => $hopDongCuoi->email_sdt_co_dau,
            'buoi_chup' => $hopDongCuoi->buoi_chup,
            'ngay_chup_du_kien' => optional($hopDongCuoi->ngay_chup_du_kien)->format('Y-m-d'),
            'kenh_tiep_can' => $hopDongCuoi->kenh_tiep_can,
            'ngay_cuoi_du_kien' => optional($hopDongCuoi->ngay_cuoi_du_kien)->format('Y-m-d'),
            'ngay_tra_link_demo_du_kien' => optional($hopDongCuoi->ngay_tra_link_demo_du_kien)->format('Y-m-d'),
            'ngay_tra_link_in_du_kien' => optional($hopDongCuoi->ngay_tra_link_in_du_kien)->format('Y-m-d'),
            'yeu_cau_dac_biet' => $hopDongCuoi->yeu_cau_dac_biet,
            'tong_tien' => $hopDongCuoi->tong_tien,
            'tien_coc' => $hopDongCuoi->tien_coc,
            'chiet_khau' => $hopDongCuoi->chiet_khau,
            'ngay_ky_hop_dong' => optional($hopDongCuoi->ngay_ky_hop_dong)->format('Y-m-d'),
            'han_thanh_toan_lan2' => optional($hopDongCuoi->han_thanh_toan_lan2)->format('Y-m-d'),
            'han_thanh_toan_lan3' => optional($hopDongCuoi->han_thanh_toan_lan3)->format('Y-m-d'),
            'concept_id' => $hopDongCuoi->concept_id,
            'loai_dich_vu' => $hopDongCuoi->loai_dich_vu,
            'nhom_dich_vu_id' => $hopDongCuoi->nhom_dich_vu_id !== null ? (int) $hopDongCuoi->nhom_dich_vu_id : null,
            /** Combo & nâng cấp: dich_vu_le_id trong combo có trang_thai_su_dung = 1 (bảng hop_dong_cuoi_nhom_dich_vu). */
            'combo_dich_vu_checked_ids' => $hopDongCuoi->loai_dich_vu === 'combo_va_nang_cap'
                ? $hopDongCuoi->hopDongCuoiNhomDichVu
                    ->filter(static fn ($r) => (int) $r->trang_thai_su_dung === 1)
                    ->pluck('dich_vu_le_id')
                    ->map(static fn ($id) => (int) $id)
                    ->values()
                    ->all()
                : [],
            /** Hàng trong hop_dong_cuoi_dich_vu_le: ghép lẻ = toàn bộ; combo nâng cấp = chỉ dịch vụ lẻ thêm (nâng cấp). */
            'hop_dong_cuoi_dich_vu_le' => $hopDongCuoi->hopDongCuoiDichVuLe
                ->map(static fn (HopDongCuoiDichVuLe $r) => [
                    'id' => (int) $r->dich_vu_le_id,
                    'so_luong' => (int) $r->so_luong,
                ])
                ->values()
                ->all(),
            'trang_phuc_ids' => $hopDongCuoi->hopDongCuoiTrangPhuc
                ->pluck('trang_phuc_id')
                ->map(static fn ($id) => (int) $id)
                ->values()
                ->all(),
            'thanh_vien_nhan_vien_ids' => ($hopDongCuoi->relationLoaded('thanhVienHopDongCuis')
                ? $hopDongCuoi->thanhVienHopDongCuis
                : $hopDongCuoi->thanhVienHopDongCuis()->get())
                ->where('vai_tro', ThanhVienHopDongCuoi::VAI_TRO_THANH_VIEN)
                ->pluck('nhan_vien_id')
                ->map(static fn ($id) => (int) $id)
                ->values()
                ->all(),
        ];
    }
}

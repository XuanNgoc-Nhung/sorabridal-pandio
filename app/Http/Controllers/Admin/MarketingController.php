<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HopDongCuoi;
use App\Models\NhanVien;
use App\Models\NoteKhachMoi;
use App\Support\AdminPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MarketingController extends Controller
{
    public function noteKhachMoi(Request $request)
    {
        $validated = $request->validate([
            'tu_khoa' => 'nullable|string|max:200',
            'trang_thai' => 'nullable|string|in:'.implode(',', array_keys(NoteKhachMoi::trangThaiLabels())),
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(NoteKhachMoi::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = NoteKhachMoi::query()
            ->with(['phuTrachSale', 'phuTrachSales', 'nguoiTao']);

        $tuKhoa = trim((string) ($validated['tu_khoa'] ?? ''));
        if ($tuKhoa !== '') {
            $likeTuKhoa = '%'.addcslashes($tuKhoa, '%_\\').'%';
            $query->where(function ($qb) use ($likeTuKhoa) {
                $qb->where('ten_khach', 'like', $likeTuKhoa)
                    ->orWhere('so_dien_thoai', 'like', $likeTuKhoa)
                    ->orWhere('nguon_khach', 'like', $likeTuKhoa)
                    ->orWhere('ly_do_khong_chot', 'like', $likeTuKhoa)
                    ->orWhereHas('phuTrachSale', fn ($q) => $q->where('name', 'like', $likeTuKhoa))
                    ->orWhereHas('phuTrachSales', fn ($q) => $q->where('name', 'like', $likeTuKhoa))
                    ->orWhereHas('nguoiTao', fn ($q) => $q->where('name', 'like', $likeTuKhoa));
            });
        }

        if (! empty($validated['trang_thai'])) {
            $query->where('trang_thai', $validated['trang_thai']);
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? NoteKhachMoi::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, NoteKhachMoi::SAP_XEP_OPTIONS)) {
            $sapXepTheo = NoteKhachMoi::SAP_XEP_MAC_DINH;
        }
        $thuTu = $validated['thu_tu'] ?? 'desc';

        match ($sapXepTheo) {
            NoteKhachMoi::SAP_XEP_NGAY_HEN_LICH => $query->orderBy('ngay_hen_lich', $thuTu),
            NoteKhachMoi::SAP_XEP_NGAY_DEN_THUC_TE => $query->orderBy('ngay_den_thuc_te', $thuTu),
            NoteKhachMoi::SAP_XEP_TEN_KHACH => $query->orderBy('ten_khach', $thuTu),
            NoteKhachMoi::SAP_XEP_SO_DIEN_THOAI => $query->orderBy('so_dien_thoai', $thuTu),
            NoteKhachMoi::SAP_XEP_NGUON_KHACH => $query->orderBy('nguon_khach', $thuTu),
            NoteKhachMoi::SAP_XEP_TRANG_THAI => $query->orderBy('trang_thai', $thuTu),
            default => $query->orderBy('created_at', $thuTu),
        };
        $query->orderBy('id', $thuTu);

        $danhSach = $query
            ->paginate(AdminPagination::perPage())
            ->withQueryString();

        $traCuuHopDongTheoSdt = NoteKhachMoi::mapTraCuuHopDongTheoSoDienThoai(
            $danhSach->getCollection()->pluck('so_dien_thoai')
        );

        $danhSachNhanVien = NhanVien::query()
            ->with('user')
            ->whereHas('user')
            ->orderBy('id')
            ->get();

        $trangThaiLabels = NoteKhachMoi::trangThaiLabels();
        $kenhTiepCanLabels = HopDongCuoi::kenhTiepCanLabels();

        return view('admin.marketing.note-khach-moi', compact(
            'danhSach',
            'danhSachNhanVien',
            'trangThaiLabels',
            'kenhTiepCanLabels',
            'traCuuHopDongTheoSdt',
        ));
    }

    public function timHopDongTheoSdt(Request $request): JsonResponse
    {
        return response()->json(
            HopDongCuoi::lookupPayloadByContactPhone($request->input('so_dien_thoai'))
        );
    }

    public function storeNoteKhachMoi(Request $request)
    {
        $saleNhanVienIds = $request->input('phu_trach_sale_nhan_vien_ids', []);
        $data = $this->validatedNoteKhachMoi($request);
        $data['nguoi_tao_id'] = $request->user()?->id;

        $note = NoteKhachMoi::create($data);
        $this->syncPhuTrachSales($note, is_array($saleNhanVienIds) ? $saleNhanVienIds : []);

        return redirect()
            ->route('admin.note-khach-moi')
            ->with('success', 'Đã thêm note khách mới thành công.');
    }

    public function updateNoteKhachMoi(Request $request, NoteKhachMoi $noteKhachMoi)
    {
        $saleNhanVienIds = $request->input('phu_trach_sale_nhan_vien_ids', []);
        $noteKhachMoi->update($this->validatedNoteKhachMoi($request));
        $this->syncPhuTrachSales($noteKhachMoi, is_array($saleNhanVienIds) ? $saleNhanVienIds : []);

        return redirect()
            ->route('admin.note-khach-moi')
            ->with('success', 'Đã cập nhật note khách mới thành công.');
    }

    public function destroyNoteKhachMoi(NoteKhachMoi $noteKhachMoi)
    {
        $noteKhachMoi->delete();

        return redirect()
            ->route('admin.note-khach-moi')
            ->with('success', 'Đã xoá note khách mới.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedNoteKhachMoi(Request $request): array
    {
        $trangThaiKeys = array_keys(NoteKhachMoi::trangThaiLabels());

        $request->merge([
            'nguon_khach' => $request->filled('nguon_khach') ? $request->input('nguon_khach') : null,
        ]);

        $data = $request->validate([
            'ten_khach' => 'nullable|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'phu_trach_sale_id' => 'nullable|integer|exists:users,id',
            'phu_trach_sale_nhan_vien_ids' => 'nullable|array',
            'phu_trach_sale_nhan_vien_ids.*' => 'integer|exists:nhan_vien,id',
            'ngay_hen_lich' => 'nullable|date_format:Y-m-d H:i',
            'ngay_den_thuc_te' => 'nullable|date',
            'nguon_khach' => 'nullable|string|max:255',
            'trang_thai' => ['nullable', 'string', Rule::in($trangThaiKeys)],
            'ly_do_khong_chot' => [
                'nullable',
                'string',
                'max:5000',
                Rule::requiredIf(fn () => $request->input('trang_thai') === NoteKhachMoi::TRANG_THAI_KHONG_CHOT),
            ],
        ], [
            'ngay_hen_lich.date_format' => 'Ngày hẹn lịch không hợp lệ.',
            'ngay_den_thuc_te.date' => 'Ngày đến thực tế không hợp lệ.',
            'phu_trach_sale_id.exists' => 'Phụ trách sale không hợp lệ.',
            'phu_trach_sale_nhan_vien_ids.*.exists' => 'Nhân viên sale không hợp lệ.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
            'ly_do_khong_chot.required' => 'Vui lòng nhập lý do không chốt.',
        ]);

        if (array_key_exists('nguon_khach', $data)) {
            $data['nguon_khach'] = HopDongCuoi::normalizeNguonKhachInput($data['nguon_khach']);
        }

        if (! in_array($data['trang_thai'] ?? null, NoteKhachMoi::trangThaiCanLyDoKhongChot(), true)) {
            $data['ly_do_khong_chot'] = null;
        }

        unset($data['phu_trach_sale_nhan_vien_ids']);

        foreach (['phu_trach_sale_id', 'ngay_hen_lich', 'ngay_den_thuc_te', 'nguon_khach', 'trang_thai', 'ly_do_khong_chot'] as $key) {
            if (array_key_exists($key, $data) && ($data[$key] === '' || $data[$key] === null)) {
                $data[$key] = null;
            }
        }

        return $data;
    }

    /**
     * @param  list<int|string>  $nhanVienIds
     */
    private function syncPhuTrachSales(NoteKhachMoi $note, array $nhanVienIds): void
    {
        $nhanVienIds = collect($nhanVienIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $userIds = NhanVien::query()
            ->whereIn('id', $nhanVienIds)
            ->whereHas('user')
            ->with('user:id')
            ->get()
            ->pluck('user.id')
            ->filter()
            ->unique()
            ->values();

        $note->phuTrachSales()->sync($userIds);
        $note->update(['phu_trach_sale_id' => $userIds->first()]);
    }
}

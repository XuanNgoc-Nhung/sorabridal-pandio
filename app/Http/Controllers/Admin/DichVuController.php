<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DichVuLe;
use App\Models\NhomDichVu;
use App\Models\PhongBan;
use App\Support\AdminPagination;
use App\Support\LoaiCuoiPhongSu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DichVuController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.dich-vu.dich-vu-le');
    }

    public function dichVuLe(Request $request)
    {
        $query = DichVuLe::query()->with(['nguoiTao']);

        if ($request->filled('search')) {
            $q = $request->input('search');
            $query->where(function ($qb) use ($q) {
                $qb->where('ten_dich_vu', 'like', '%'.$q.'%')
                    ->orWhere('ma_dich_vu', 'like', '%'.$q.'%');
            });
        }

        if ($request->filled('trang_thai') && in_array((string) $request->input('trang_thai'), ['0', '1'], true)) {
            $query->where('trang_thai', (int) $request->input('trang_thai'));
        }

        if ($request->filled('phong_ban_id')) {
            $phongBanId = (int) $request->input('phong_ban_id');
            $phongBan = PhongBan::find($phongBanId);
            if ($phongBan) {
                $query->coPhongBan($phongBanId, $phongBan->ma_phong_ban);
            }
        }

        if ($request->filled('loai') && in_array($request->input('loai'), LoaiCuoiPhongSu::values(), true)) {
            $query->where('loai', $request->input('loai'));
        }

        $sapXepTheo = $request->input('sap_xep_theo', DichVuLe::SAP_XEP_TEN);
        if (! in_array($sapXepTheo, [DichVuLe::SAP_XEP_TEN, DichVuLe::SAP_XEP_GIA], true)) {
            $sapXepTheo = DichVuLe::SAP_XEP_TEN;
        }
        $thuTu = strtolower((string) $request->input('thu_tu', 'asc')) === 'desc' ? 'desc' : 'asc';

        $cotSapXep = $sapXepTheo === DichVuLe::SAP_XEP_GIA ? 'gia_dich_vu' : 'ten_dich_vu';
        $query->orderBy($cotSapXep, $thuTu);

        $danhSach = $query->paginate(AdminPagination::perPage())->withQueryString();
        $phongBans = PhongBan::orderBy('ten_phong_ban')->get();

        return view('admin.dich-vu.dich-vu-le', compact('danhSach', 'phongBans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'ma_dich_vu' => ['required', 'string', 'max:50', Rule::unique('dich_vu_le', 'ma_dich_vu')],
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'nullable|integer|in:0,1',
            'ghi_chu' => 'nullable|string',
            'gia_dich_vu' => 'required|numeric|min:0',
            'loai' => 'required|string|in:'.implode(',', LoaiCuoiPhongSu::values()),
            'phong_ban_id' => 'required|array|min:1',
            'phong_ban_id.*' => 'integer|exists:phong_ban,id',
        ], [
            'ten_dich_vu.required' => 'Vui lòng nhập tên dịch vụ.',
            'ten_dich_vu.string' => 'Tên dịch vụ phải là chuỗi ký tự.',
            'ten_dich_vu.max' => 'Tên dịch vụ không được quá 255 ký tự.',
            'ma_dich_vu.required' => 'Vui lòng nhập mã dịch vụ.',
            'ma_dich_vu.string' => 'Mã dịch vụ phải là chuỗi ký tự.',
            'ma_dich_vu.max' => 'Mã dịch vụ không được quá 50 ký tự.',
            'ma_dich_vu.unique' => 'Mã dịch vụ đã tồn tại, vui lòng chọn mã khác.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
            'trang_thai.integer' => 'Trạng thái phải là số nguyên.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự.',
            'gia_dich_vu.required' => 'Vui lòng nhập giá dịch vụ.',
            'gia_dich_vu.numeric' => 'Giá dịch vụ phải là số.',
            'gia_dich_vu.min' => 'Giá dịch vụ không được âm.',
            'loai.required' => 'Vui lòng chọn loại dịch vụ.',
            'loai.in' => 'Loại dịch vụ không hợp lệ.',
            'phong_ban_id.required' => 'Vui lòng chọn ít nhất một phòng ban phụ trách.',
            'phong_ban_id.min' => 'Vui lòng chọn ít nhất một phòng ban phụ trách.',
            'phong_ban_id.*.integer' => 'Phòng ban không hợp lệ.',
            'phong_ban_id.*.exists' => 'Phòng ban không tồn tại.',
        ]);

        DichVuLe::create([
            'ten_dich_vu' => $request->input('ten_dich_vu'),
            'ma_dich_vu' => $request->input('ma_dich_vu'),
            'mo_ta' => $request->input('mo_ta'),
            'trang_thai' => (int) $request->input('trang_thai', DichVuLe::TRANG_THAI_HIEN_THI),
            'ghi_chu' => $request->input('ghi_chu'),
            'gia_dich_vu' => $request->input('gia_dich_vu'),
            'loai' => $request->input('loai'),
            'phong_ban_id' => $this->formatPhongBanId($request->input('phong_ban_id', [])),
            'nguoi_tao_id' => $request->user()?->id,
        ]);

        return redirect()->route('admin.dich-vu.dich-vu-le')->with('success', 'Đã thêm dịch vụ lẻ thành công.');
    }

    public function update(Request $request, DichVuLe $dichVu)
    {
        $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'ma_dich_vu' => ['required', 'string', 'max:50', Rule::unique('dich_vu_le', 'ma_dich_vu')->ignore($dichVu->id)],
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'nullable|integer|in:0,1',
            'ghi_chu' => 'nullable|string',
            'gia_dich_vu' => 'required|numeric|min:0',
            'loai' => 'required|string|in:'.implode(',', LoaiCuoiPhongSu::values()),
            'phong_ban_id' => 'required|array|min:1',
            'phong_ban_id.*' => 'integer|exists:phong_ban,id',
        ], [
            'ten_dich_vu.required' => 'Vui lòng nhập tên dịch vụ.',
            'ten_dich_vu.string' => 'Tên dịch vụ phải là chuỗi ký tự.',
            'ten_dich_vu.max' => 'Tên dịch vụ không được quá 255 ký tự.',
            'ma_dich_vu.required' => 'Vui lòng nhập mã dịch vụ.',
            'ma_dich_vu.string' => 'Mã dịch vụ phải là chuỗi ký tự.',
            'ma_dich_vu.max' => 'Mã dịch vụ không được quá 50 ký tự.',
            'ma_dich_vu.unique' => 'Mã dịch vụ đã tồn tại, vui lòng chọn mã khác.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
            'trang_thai.integer' => 'Trạng thái phải là số nguyên.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự.',
            'gia_dich_vu.required' => 'Vui lòng nhập giá dịch vụ.',
            'gia_dich_vu.numeric' => 'Giá dịch vụ phải là số.',
            'gia_dich_vu.min' => 'Giá dịch vụ không được âm.',
            'loai.required' => 'Vui lòng chọn loại dịch vụ.',
            'loai.in' => 'Loại dịch vụ không hợp lệ.',
            'phong_ban_id.required' => 'Vui lòng chọn ít nhất một phòng ban phụ trách.',
            'phong_ban_id.min' => 'Vui lòng chọn ít nhất một phòng ban phụ trách.',
            'phong_ban_id.*.integer' => 'Phòng ban không hợp lệ.',
            'phong_ban_id.*.exists' => 'Phòng ban không tồn tại.',
        ]);

        $dichVu->update([
            'ten_dich_vu' => $request->input('ten_dich_vu'),
            'ma_dich_vu' => $request->input('ma_dich_vu'),
            'mo_ta' => $request->input('mo_ta'),
            'trang_thai' => (int) $request->input('trang_thai', DichVuLe::TRANG_THAI_HIEN_THI),
            'ghi_chu' => $request->input('ghi_chu'),
            'gia_dich_vu' => $request->input('gia_dich_vu'),
            'loai' => $request->input('loai'),
            'phong_ban_id' => $this->formatPhongBanId($request->input('phong_ban_id', [])),
        ]);

        return redirect()->route('admin.dich-vu.dich-vu-le')->with('success', 'Đã cập nhật dịch vụ lẻ thành công.');
    }

    public function destroy(DichVuLe $dichVu)
    {
        $dichVu->delete();

        return redirect()->route('admin.dich-vu.dich-vu-le')->with('success', 'Đã xóa dịch vụ lẻ.');
    }

    /**
     * Gộp mảng id phòng ban thành chuỗi lưu DB (ví dụ "1,2,5").
     *
     * @param  array<int|string>  $ids
     */
    private function formatPhongBanId(array $ids): ?string
    {
        $normalized = array_values(array_unique(array_filter(array_map('intval', $ids))));

        return $normalized === [] ? null : implode(',', $normalized);
    }

    public function nhomDichVu(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:200',
            'trang_thai' => 'nullable|in:0,1',
            'loai' => 'nullable|string|in:'.implode(',', LoaiCuoiPhongSu::values()),
            'dich_vu_le_id' => 'nullable|integer|exists:dich_vu_le,id',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(NhomDichVu::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = NhomDichVu::query()->with(['dichVuLe', 'nguoiTao']);

        $search = trim((string) ($validated['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_nhom', 'like', $like)
                    ->orWhere('ma_nhom', 'like', $like)
                    ->orWhere('ghi_chu', 'like', $like)
                    ->orWhere('mo_ta', 'like', $like)
                    ->orWhere('the', 'like', $like);
            });
        }

        if ($request->filled('trang_thai') && in_array((string) $request->input('trang_thai'), ['0', '1'], true)) {
            $query->where('trang_thai', (int) $request->input('trang_thai'));
        }

        if (! empty($validated['loai'])) {
            $query->where('loai', $validated['loai']);
        }

        if ($request->filled('dich_vu_le_id')) {
            $dichVuLeId = (int) $request->input('dich_vu_le_id');
            $query->whereHas('dichVuLe', function ($dvq) use ($dichVuLeId) {
                $dvq->where('dich_vu_le.id', $dichVuLeId);
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? NhomDichVu::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, NhomDichVu::SAP_XEP_OPTIONS)) {
            $sapXepTheo = NhomDichVu::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';

        match ($sapXepTheo) {
            NhomDichVu::SAP_XEP_TEN => $query->orderBy('ten_nhom', $thuTu),
            NhomDichVu::SAP_XEP_GIA_TIEN => $query->orderBy('gia_tien', $thuTu),
            NhomDichVu::SAP_XEP_GIA_GOC => $query->orderBy('gia_goc', $thuTu),
            default => $query->orderBy('ten_nhom', $thuTu),
        };

        $danhSach = $query->paginate(AdminPagination::perPage())->withQueryString();

        $tatCaDichVuLe = DichVuLe::query()->orderBy('ten_dich_vu')->get();

        return view('admin.dich-vu.nhom-dich-vu', compact('danhSach', 'tatCaDichVuLe'));
    }

    public function storeNhomDichVu(Request $request)
    {
        $request->validate([
            'ten_nhom' => 'required|string|max:255',
            'ma_nhom' => 'nullable|string|max:50',
            'gia_tien' => 'nullable|numeric|min:0',
            'the' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'nullable|integer|in:0,1',
            'loai' => 'required|string|in:'.implode(',', LoaiCuoiPhongSu::values()),
            'dich_vu_le_ids' => 'nullable|array',
            'dich_vu_le_ids.*' => 'integer|exists:dich_vu_le,id',
        ], [
            'ten_nhom.required' => 'Vui lòng nhập tên nhóm dịch vụ.',
            'ten_nhom.string' => 'Tên nhóm phải là chuỗi ký tự.',
            'ten_nhom.max' => 'Tên nhóm không được quá 255 ký tự.',
            'ma_nhom.string' => 'Mã nhóm phải là chuỗi ký tự.',
            'ma_nhom.max' => 'Mã nhóm không được quá 50 ký tự.',
            'gia_tien.numeric' => 'Giá tiền phải là số.',
            'gia_tien.min' => 'Giá tiền không được âm.',
            'the.string' => 'Thẻ phải là chuỗi ký tự.',
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
            'trang_thai.integer' => 'Trạng thái phải là số nguyên.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
            'loai.required' => 'Vui lòng chọn loại dịch vụ.',
            'loai.in' => 'Loại dịch vụ không hợp lệ.',
            'dich_vu_le_ids.array' => 'Danh sách dịch vụ lẻ không hợp lệ.',
            'dich_vu_le_ids.*.integer' => 'Mỗi dịch vụ lẻ phải là số nguyên.',
            'dich_vu_le_ids.*.exists' => 'Một hoặc nhiều dịch vụ lẻ không tồn tại trong hệ thống.',
        ]);

        $ids = array_map('intval', (array) $request->input('dich_vu_le_ids', []));
        $loai = $request->input('loai');
        if (! empty($ids)) {
            $mismatchCount = DichVuLe::query()
                ->whereIn('id', $ids)
                ->where('loai', '!=', $loai)
                ->count();
            if ($mismatchCount > 0) {
                return redirect()->back()
                    ->withErrors(['dich_vu_le_ids' => 'Các dịch vụ lẻ đã chọn phải cùng loại với nhóm dịch vụ.'])
                    ->withInput();
            }
        }
        $giaGoc = empty($ids)
            ? 0
            : (float) DichVuLe::whereIn('id', $ids)->sum('gia_dich_vu');

        $nhomDichVu = NhomDichVu::create([
            'ten_nhom' => $request->input('ten_nhom'),
            'ma_nhom' => $request->input('ma_nhom'),
            'loai' => $loai,
            'gia_tien' => $request->input('gia_tien'),
            'gia_goc' => $giaGoc,
            'the' => $request->input('the'),
            'ghi_chu' => $request->input('ghi_chu'),
            'mo_ta' => $request->input('mo_ta'),
            'trang_thai' => (int) $request->input('trang_thai', NhomDichVu::TRANG_THAI_HIEN_THI),
            'nguoi_tao_id' => $request->user()?->id,
        ]);

        if (! empty($ids)) {
            $nhomDichVu->dichVuLe()->attach(collect($ids)->mapWithKeys(fn ($id) => [$id => ['so_luong' => 1]])->all());
        }

        return redirect()->route('admin.dich-vu.nhom-dich-vu')->with('success', 'Đã thêm nhóm dịch vụ thành công.');
    }

    public function updateNhomDichVu(Request $request, NhomDichVu $nhomDichVu)
    {
        $request->validate([
            'ten_nhom' => 'required|string|max:255',
            'ma_nhom' => 'nullable|string|max:50',
            'gia_tien' => 'nullable|numeric|min:0',
            'the' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'nullable|integer|in:0,1',
            'loai' => 'required|string|in:'.implode(',', LoaiCuoiPhongSu::values()),
            'dich_vu_le_ids' => 'nullable|array',
            'dich_vu_le_ids.*' => 'integer|exists:dich_vu_le,id',
        ], [
            'ten_nhom.required' => 'Vui lòng nhập tên nhóm dịch vụ.',
            'ten_nhom.string' => 'Tên nhóm phải là chuỗi ký tự.',
            'ten_nhom.max' => 'Tên nhóm không được quá 255 ký tự.',
            'ma_nhom.string' => 'Mã nhóm phải là chuỗi ký tự.',
            'ma_nhom.max' => 'Mã nhóm không được quá 50 ký tự.',
            'gia_tien.numeric' => 'Giá tiền phải là số.',
            'gia_tien.min' => 'Giá tiền không được âm.',
            'the.string' => 'Thẻ phải là chuỗi ký tự.',
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
            'trang_thai.integer' => 'Trạng thái phải là số nguyên.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
            'loai.required' => 'Vui lòng chọn loại dịch vụ.',
            'loai.in' => 'Loại dịch vụ không hợp lệ.',
            'dich_vu_le_ids.array' => 'Danh sách dịch vụ lẻ không hợp lệ.',
            'dich_vu_le_ids.*.integer' => 'Mỗi dịch vụ lẻ phải là số nguyên.',
            'dich_vu_le_ids.*.exists' => 'Một hoặc nhiều dịch vụ lẻ không tồn tại trong hệ thống.',
        ]);

        $ids = array_map('intval', (array) $request->input('dich_vu_le_ids', []));
        $loai = $request->input('loai');
        if (! empty($ids)) {
            $mismatchCount = DichVuLe::query()
                ->whereIn('id', $ids)
                ->where('loai', '!=', $loai)
                ->count();
            if ($mismatchCount > 0) {
                return redirect()->back()
                    ->withErrors(['dich_vu_le_ids' => 'Các dịch vụ lẻ đã chọn phải cùng loại với nhóm dịch vụ.'])
                    ->withInput();
            }
        }
        $giaGoc = empty($ids)
            ? 0
            : (float) DichVuLe::whereIn('id', $ids)->sum('gia_dich_vu');

        $nhomDichVu->update([
            'ten_nhom' => $request->input('ten_nhom'),
            'ma_nhom' => $request->input('ma_nhom'),
            'loai' => $loai,
            'gia_tien' => $request->input('gia_tien'),
            'gia_goc' => $giaGoc,
            'the' => $request->input('the'),
            'ghi_chu' => $request->input('ghi_chu'),
            'mo_ta' => $request->input('mo_ta'),
            'trang_thai' => (int) $request->input('trang_thai', NhomDichVu::TRANG_THAI_HIEN_THI),
        ]);

        $nhomDichVu->dichVuLe()->sync(collect($ids)->mapWithKeys(fn ($id) => [$id => ['so_luong' => 1]])->all());

        return redirect()->route('admin.dich-vu.nhom-dich-vu')->with('success', 'Đã cập nhật nhóm dịch vụ thành công.');
    }

    public function destroyNhomDichVu(NhomDichVu $nhomDichVu)
    {
        $nhomDichVu->dichVuLe()->detach();
        $nhomDichVu->delete();

        return redirect()->route('admin.dich-vu.nhom-dich-vu')->with('success', 'Đã xóa nhóm dịch vụ.');
    }

    /**
     * Danh sách dịch vụ lẻ theo loại (JSON) cho modal nhóm dịch vụ.
     */
    public function listDichVuLeTheoLoai(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'loai' => 'required|string|in:'.implode(',', LoaiCuoiPhongSu::values()),
        ]);

        $items = DichVuLe::query()
            ->where('loai', $validated['loai'])
            ->orderBy('ten_dich_vu')
            ->get(['id', 'ten_dich_vu', 'ma_dich_vu', 'gia_dich_vu', 'loai'])
            ->map(fn (DichVuLe $dv) => [
                'id' => $dv->id,
                'ten_dich_vu' => $dv->ten_dich_vu,
                'ma_dich_vu' => $dv->ma_dich_vu,
                'gia_dich_vu' => $dv->gia_dich_vu !== null ? (float) $dv->gia_dich_vu : 0,
                'loai' => $dv->loai,
            ])
            ->values();

        return response()->json(['items' => $items]);
    }
}

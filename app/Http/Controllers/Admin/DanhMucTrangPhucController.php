<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMucTrangPhuc;
use App\Support\AdminPagination;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DanhMucTrangPhucController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(DanhMucTrangPhuc::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = DanhMucTrangPhuc::query()->withCount('trangPhucs');

        $search = trim((string) ($validated['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_danh_muc', 'like', $like)
                    ->orWhere('ma_danh_muc', 'like', $like);
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? DanhMucTrangPhuc::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, DanhMucTrangPhuc::SAP_XEP_OPTIONS)) {
            $sapXepTheo = DanhMucTrangPhuc::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            DanhMucTrangPhuc::SAP_XEP_TEN => $query->orderBy('ten_danh_muc', $thuTu),
            DanhMucTrangPhuc::SAP_XEP_MA => $query->orderBy('ma_danh_muc', $thuTu),
            default => $query->orderBy('id', $thuTu),
        };

        $danhSach = $query->paginate(AdminPagination::perPage())->withQueryString();

        return view('admin.trang-phuc.loai-trang-phuc', compact('danhSach'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'ma_danh_muc' => ['required', 'string', 'max:50', Rule::unique('danh_muc_trang_phuc', 'ma_danh_muc')],
            'ghi_chu' => 'nullable|string',
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'ma_danh_muc.required' => 'Vui lòng nhập mã danh mục.',
            'ma_danh_muc.unique' => 'Mã danh mục đã tồn tại, vui lòng chọn mã khác.',
        ]);

        DanhMucTrangPhuc::create([
            'ten_danh_muc' => $request->input('ten_danh_muc'),
            'ma_danh_muc' => $request->input('ma_danh_muc'),
            'ghi_chu' => $request->input('ghi_chu'),
        ]);

        return redirect()->route('admin.trang-phuc.loai-trang-phuc')->with('success', 'Đã thêm loại trang phục thành công.');
    }

    public function update(Request $request, DanhMucTrangPhuc $danhMucTrangPhuc)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'ma_danh_muc' => ['required', 'string', 'max:50', Rule::unique('danh_muc_trang_phuc', 'ma_danh_muc')->ignore($danhMucTrangPhuc->id)],
            'ghi_chu' => 'nullable|string',
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'ma_danh_muc.required' => 'Vui lòng nhập mã danh mục.',
            'ma_danh_muc.unique' => 'Mã danh mục đã tồn tại, vui lòng chọn mã khác.',
        ]);

        $danhMucTrangPhuc->update([
            'ten_danh_muc' => $request->input('ten_danh_muc'),
            'ma_danh_muc' => $request->input('ma_danh_muc'),
            'ghi_chu' => $request->input('ghi_chu'),
        ]);

        return redirect()->route('admin.trang-phuc.loai-trang-phuc')->with('success', 'Đã cập nhật loại trang phục thành công.');
    }

    public function destroy(DanhMucTrangPhuc $danhMucTrangPhuc)
    {
        if ($danhMucTrangPhuc->trangPhucs()->exists()) {
            return redirect()
                ->route('admin.trang-phuc.loai-trang-phuc')
                ->with('error', 'Đang có sản phẩm trang phục thuộc loại này. Không thể xoá.');
        }

        $danhMucTrangPhuc->delete();

        return redirect()->route('admin.trang-phuc.loai-trang-phuc')->with('success', 'Đã xóa loại trang phục.');
    }
}

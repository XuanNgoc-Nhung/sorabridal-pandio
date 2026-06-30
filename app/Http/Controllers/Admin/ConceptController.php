<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concept;
use App\Support\AdminPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ConceptController extends Controller
{
    public function concept(Request $request)
    {
        $diaDiemKeys = array_keys(config('concept.dia_diem', []));

        $validated = $request->validate([
            'search' => 'nullable|string|max:200',
            'trang_thai' => 'nullable|in:0,1',
            'dia_diem' => 'nullable|string|in:'.implode(',', $diaDiemKeys),
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(Concept::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]);

        $query = Concept::query()->withCount(['hopDongCuoi as so_luot_su_dung']);

        $search = trim((string) ($validated['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where('ten_concept', 'like', $like);
        }

        if ($request->filled('trang_thai') && in_array((string) $request->input('trang_thai'), ['0', '1'], true)) {
            $query->where('trang_thai', (int) $request->input('trang_thai'));
        }

        $diaDiem = $validated['dia_diem'] ?? null;
        if ($diaDiem !== null && in_array($diaDiem, $diaDiemKeys, true)) {
            $query->where('dia_diem', $diaDiem);
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? Concept::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, Concept::SAP_XEP_OPTIONS)) {
            $sapXepTheo = Concept::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';

        match ($sapXepTheo) {
            Concept::SAP_XEP_TEN => $query->orderBy('ten_concept', $thuTu),
            Concept::SAP_XEP_DA_SU_DUNG => $query->orderBy('so_luot_su_dung', $thuTu),
            Concept::SAP_XEP_TRANG_THAI => $query->orderBy('trang_thai', $thuTu),
            default => $query->orderBy('id', $thuTu),
        };

        $danhSach = $query->paginate(AdminPagination::perPage())->withQueryString();

        return view('admin.concept.concept', compact('danhSach'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_concept' => 'required|string|max:255',
            'ma_concept' => ['required', 'string', 'max:50', Rule::unique('concept', 'ma_concept')],
            'dia_diem' => 'required|string|in:'.implode(',', array_keys(config('concept.dia_diem', []))),
            'hinh_anh' => 'nullable|image|max:10240',
            'trang_thai' => 'required|integer|in:0,1',
        ], [
            'ma_concept.required' => 'Vui lòng nhập mã concept.',
            'ma_concept.unique' => 'Mã concept đã tồn tại, vui lòng chọn mã khác.',
        ]);

        $hinhAnhPath = null;
        if ($request->hasFile('hinh_anh')) {
            $hinhAnhPath = $request->file('hinh_anh')->store('concept', 'public');
        }

        Concept::create([
            'ten_concept' => $validated['ten_concept'],
            'ma_concept' => $validated['ma_concept'],
            'dia_diem' => $validated['dia_diem'],
            'hinh_anh' => $hinhAnhPath,
            'trang_thai' => (int) $validated['trang_thai'],
        ]);

        return redirect()
            ->route('admin.concept.concept')
            ->with('success', 'Đã thêm concept thành công.');
    }

    public function update(Request $request, Concept $concept)
    {
        $validated = $request->validate([
            'ten_concept' => 'required|string|max:255',
            'ma_concept' => ['required', 'string', 'max:50', Rule::unique('concept', 'ma_concept')->ignore($concept->id)],
            'dia_diem' => 'required|string|in:'.implode(',', array_keys(config('concept.dia_diem', []))),
            'hinh_anh' => 'nullable|image|max:10240',
            'trang_thai' => 'required|integer|in:0,1',
        ], [
            'ma_concept.required' => 'Vui lòng nhập mã concept.',
            'ma_concept.unique' => 'Mã concept đã tồn tại, vui lòng chọn mã khác.',
        ]);

        $updateData = [
            'ten_concept' => $validated['ten_concept'],
            'ma_concept' => $validated['ma_concept'],
            'dia_diem' => $validated['dia_diem'],
            'trang_thai' => (int) $validated['trang_thai'],
        ];

        if ($request->hasFile('hinh_anh')) {
            $newPath = $request->file('hinh_anh')->store('concept', 'public');

            if (! empty($concept->hinh_anh) && Storage::disk('public')->exists($concept->hinh_anh)) {
                Storage::disk('public')->delete($concept->hinh_anh);
            }

            $updateData['hinh_anh'] = $newPath;
        }

        $concept->update($updateData);

        return redirect()
            ->route('admin.concept.concept')
            ->with('success', 'Đã cập nhật concept thành công.');
    }

    public function destroy(Concept $concept)
    {
        if (! empty($concept->hinh_anh) && Storage::disk('public')->exists($concept->hinh_anh)) {
            Storage::disk('public')->delete($concept->hinh_anh);
        }

        $concept->delete();

        return redirect()
            ->route('admin.concept.concept')
            ->with('success', 'Đã xóa concept thành công.');
    }
}

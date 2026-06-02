<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\NhanVien;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class NhanSuController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules()));

        $query = User::query()
            ->with(['nhanVien', 'nhanVien.phongBans'])
            ->orderBy('id');

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like);
            });
        }

        return $this->apiListFromQuery($query, fn (User $user) => $this->formatNhanSu($user), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')],
            'password' => 'required|string|min:8|confirmed',
            'gioi_tinh' => 'nullable|string|in:nam,nu,khac',
            'ngay_sinh' => 'nullable|date',
            'cccd' => ['nullable', 'string', 'max:20', Rule::unique('nhan_vien', 'cccd')],
            'role' => 'nullable|integer|in:1,2,3',
            'vi_tri_lam_viec' => 'nullable|string|max:255',
            'ngay_vao_cong_ty' => 'nullable|date',
            'ngay_ky_hop_dong' => 'nullable|date',
            'luong_co_ban' => 'nullable|integer|min:0',
            'luong_tang_ca' => 'nullable|integer|min:0',
            'phong_ban_ids' => 'required|array|min:1',
            'phong_ban_ids.*' => 'exists:phong_ban,id',
            'hinh_anh' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $request->input('phone'),
                'password' => Hash::make($validated['password']),
                'role' => (int) $request->input('role', User::ROLE_NHAN_VIEN),
            ]);

            $hinhAnhPath = null;
            if ($request->hasFile('hinh_anh')) {
                $hinhAnhPath = $request->file('hinh_anh')->store('nhan-vien', 'public');
            }

            $nhanVien = NhanVien::create([
                'user_id' => $user->id,
                'hinh_anh' => $hinhAnhPath,
                'gioi_tinh' => $request->input('gioi_tinh'),
                'ngay_sinh' => $request->input('ngay_sinh'),
                'cccd' => $request->input('cccd'),
                'vi_tri_lam_viec' => $request->input('vi_tri_lam_viec'),
                'ngay_vao_cong_ty' => $request->input('ngay_vao_cong_ty'),
                'ngay_ky_hop_dong' => $request->input('ngay_ky_hop_dong'),
                'luong_co_ban' => $request->input('luong_co_ban', 50000),
                'luong_tang_ca' => $request->input('luong_tang_ca', 80000),
            ]);
            $nhanVien->phongBans()->sync($request->input('phong_ban_ids', []));

            DB::commit();

            $user->load(['nhanVien', 'nhanVien.phongBans']);

            return $this->apiSuccess(
                ['item' => $this->formatNhanSu($user)],
                'Đã thêm nhân sự mới thành công.',
                201
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gioi_tinh' => 'nullable|string|in:nam,nu,khac',
            'ngay_sinh' => 'nullable|date',
            'cccd' => ['nullable', 'string', 'max:20'],
            'role' => 'nullable|integer|in:1,2,3',
            'vi_tri_lam_viec' => 'nullable|string|max:255',
            'ngay_vao_cong_ty' => 'nullable|date',
            'ngay_ky_hop_dong' => 'nullable|date',
            'luong_co_ban' => 'nullable|integer|min:0',
            'luong_tang_ca' => 'nullable|integer|min:0',
            'phong_ban_ids' => 'required|array|min:1',
            'phong_ban_ids.*' => 'exists:phong_ban,id',
            'hinh_anh' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $validated['name'],
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
                $nhanVien->phongBans()->sync($request->input('phong_ban_ids', []));
                $nhanVien->update([
                    'hinh_anh' => $hinhAnhPath,
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
                    'gioi_tinh' => $request->input('gioi_tinh'),
                    'ngay_sinh' => $request->input('ngay_sinh'),
                    'cccd' => $request->input('cccd'),
                    'vi_tri_lam_viec' => $request->input('vi_tri_lam_viec'),
                    'ngay_vao_cong_ty' => $request->input('ngay_vao_cong_ty'),
                    'ngay_ky_hop_dong' => $request->input('ngay_ky_hop_dong'),
                    'luong_co_ban' => $request->input('luong_co_ban', 50000),
                    'luong_tang_ca' => $request->input('luong_tang_ca', 80000),
                ]);
                $nhanVien->phongBans()->sync($request->input('phong_ban_ids', []));
            }

            DB::commit();

            $user->load(['nhanVien', 'nhanVien.phongBans']);

            return $this->apiSuccess(
                ['item' => $this->formatNhanSu($user)],
                'Đã cập nhật nhân sự thành công.'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function doiMatKhau(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->input('password'))]);

        return $this->apiSuccess(message: 'Đã đổi mật khẩu thành công.');
    }

    public function destroy(User $user): JsonResponse
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

            return $this->apiSuccess(message: 'Đã xóa nhân sự thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function formatNhanSu(User $user): array
    {
        $nv = $user->nhanVien;

        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => (int) $user->role,
            'role_label' => $user->role_label,
            'nhan_vien' => $nv ? [
                'id' => (int) $nv->id,
                'hinh_anh' => $nv->hinh_anh,
                'hinh_anh_url' => $this->storageUrl($nv->hinh_anh),
                'gioi_tinh' => $nv->gioi_tinh,
                'ngay_sinh' => $nv->ngay_sinh?->format('Y-m-d'),
                'cccd' => $nv->cccd,
                'vi_tri_lam_viec' => $nv->vi_tri_lam_viec,
                'ngay_vao_cong_ty' => $nv->ngay_vao_cong_ty?->format('Y-m-d'),
                'ngay_ky_hop_dong' => $nv->ngay_ky_hop_dong?->format('Y-m-d'),
                'luong_co_ban' => (int) ($nv->luong_co_ban ?? 0),
                'luong_tang_ca' => (int) ($nv->luong_tang_ca ?? 0),
                'phong_bans' => $nv->phongBans?->map(fn ($pb) => [
                    'id' => (int) $pb->id,
                    'ten_phong_ban' => $pb->ten_phong_ban,
                    'ma_phong_ban' => $pb->ma_phong_ban,
                ])->values()->all() ?? [],
            ] : null,
        ];
    }
}

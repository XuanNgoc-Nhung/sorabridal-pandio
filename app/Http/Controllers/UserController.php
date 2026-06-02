<?php

namespace App\Http\Controllers;

use App\Models\DangKyTuVan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function home()
    {
        return view('user.index', $this->bookingFormViewData());
    }

    public function showDatLichForm()
    {
        return view('user.dat-lich', $this->bookingFormViewData());
    }

    public function storeDatLich(Request $request)
    {
        $bookingFormContext = $request->input('booking_form_context') === 'home' ? 'home' : 'dat-lich';

        $validated = $request->validate([
            'ten_co_dau' => 'required|string|max:150',
            'ten_chu_re' => 'required|string|max:150',
            'so_dien_thoai' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s().-]{9,20}$/'],
            'ngay_cuoi_du_kien' => [
                'nullable',
                'string',
                'max:10',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || trim($value) === '') {
                        return;
                    }

                    if ($this->normalizeBookingDate($value) === null) {
                        $fail('Ngày cưới dự kiến không đúng định dạng.');
                    }
                },
            ],
            'phim_truong_quan_tam' => ['nullable', 'string', 'max:100', Rule::in(DangKyTuVan::PHIM_TRUONG_OPTIONS)],
            'goi_dich_vu_quan_tam' => ['nullable', 'string', 'max:100', Rule::in(DangKyTuVan::GOI_DICH_VU_OPTIONS)],
            'ghi_chu' => 'nullable|string|max:2000',
        ]);

        if (! empty($validated['ngay_cuoi_du_kien'])) {
            $validated['ngay_cuoi_du_kien'] = $this->normalizeBookingDate($validated['ngay_cuoi_du_kien']);
        }

        DangKyTuVan::create($validated);

        return redirect()->back()
            ->with('booking_form_context', $bookingFormContext)
            ->with('success', 'Đăng ký tư vấn thành công. Sora Studio sẽ liên hệ với bạn trong vòng 2 giờ làm việc.');
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function bookingFormViewData(): array
    {
        return [
            'phimTruongOptions' => DangKyTuVan::PHIM_TRUONG_OPTIONS,
            'goiDichVuOptions' => DangKyTuVan::GOI_DICH_VU_OPTIONS,
        ];
    }

    private function normalizeBookingDate(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        foreach (['Y-m-d', 'd/m/Y'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);

                if ($date && $date->format($format) === $value) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable $exception) {
                continue;
            }
        }

        return null;
    }
}

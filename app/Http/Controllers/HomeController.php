<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect($this->checkRedirect());
    }

    private function checkRedirect(): string
    {
        $isAuthenticated = auth()->check();
        $destination = $isAuthenticated ? '/admin' : '/login';

        if ($isAuthenticated) {
            $user = Auth::user();
            $user?->refresh();
            $thongBaoRoute = $user?->routeThongBaoTrangThai();
            if ($thongBaoRoute !== null) {
                return route($thongBaoRoute, absolute: false);
            }
        }

        Log::info('HomeController redirect', [
            'authenticated' => $isAuthenticated,
            'destination' => $destination,
            'user_id' => auth()->id(),
        ]);

        return $destination;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
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

        Log::info('HomeController redirect', [
            'authenticated' => $isAuthenticated,
            'destination' => $destination,
            'user_id' => auth()->id(),
        ]);

        return $destination;
    }
}

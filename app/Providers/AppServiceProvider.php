<?php

namespace App\Providers;

use App\Models\Booking;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Mengirim jumlah peminjaman berstatus pending ke layout & halaman admin
        View::composer(['layouts.*', 'admin.*'], function ($view) {
            $pendingBookingsCount = Booking::where('status', 'pending')->count();
            $view->with('pendingBookingsCount', $pendingBookingsCount);
        });
    }
}
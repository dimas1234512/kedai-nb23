<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    // Supaya widget ini reload otomatis tiap 15 detik (biar live)
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // 1. Hitung Pemasukan Bulan Ini (Hanya yang statusnya 'paid')
        $pemasukanBulanIni = Order::where('status', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // 2. Hitung Total Order Bulan Ini (Semua status kecuali batal)
        $orderBulanIni = Order::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // 3. Pemasukan Hari Ini
        $pemasukanHariIni = Order::where('status', 'paid')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        return [
            Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($pemasukanBulanIni, 0, ',', '.'))
                ->description('Total uang masuk dari pesanan selesai')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success') // Warna Hijau
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Hiasan grafik mini

            Stat::make('Total Pesanan Bulan Ini', $orderBulanIni . ' Pesanan')
                ->description('Termasuk pesanan baru & selesai')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'), // Warna Kuning

            Stat::make('Pemasukan Hari Ini', 'Rp ' . number_format($pemasukanHariIni, 0, ',', '.'))
                ->description('Omzet hari ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'), // Warna Biru
        ];
    }
}
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Filament\Notifications\Notification; // Import ini

class OrderAlert extends Component
{
    public $lastCount = 0;

    public function mount()
    {
        // Catat jumlah awal saat halaman dibuka
        $this->lastCount = Order::count();
    }

    public function checkOrders()
    {
        $currentCount = Order::count();

        // Jika jumlah bertambah (ada order baru)
        if ($currentCount > $this->lastCount) {
            $this->lastCount = $currentCount;
            
            // 1. Kirim Notifikasi Visual (Popup)
            Notification::make()
                ->title('Pesanan Baru Masuk!')
                ->body('Cek daftar pesanan sekarang.')
                ->success()
                ->duration(10000) // Muncul 10 detik
                ->send();

            // 2. Kirim Sinyal Suara
            $this->dispatch('play-sound');
        }
    }

    public function render()
    {
        return view('livewire.order-alert');
    }
}
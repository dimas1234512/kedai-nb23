<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Title;

class SuccessPage extends Component
{
    #[Title('Struk Pesanan - Kedai NB 23')]

    public $order;

    public function mount($id)
    {
        // Cari order berdasarkan ID, kalau tidak ketemu tampilkan 404
        $this->order = Order::with('items.product')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.success-page');
    }
}
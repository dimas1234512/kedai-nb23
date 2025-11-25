<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product; 
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

class CheckoutPage extends Component
{
    use WithFileUploads;

    #[Title('Checkout - Kedai NB 23')]

    // Variabel Data
    public $cart = [];
    public $customer_name;
    public $table_number;
    public $note; 
    public $order_type = 'dine_in'; 
    public $payment_method = 'cash';
    public $payment_proof;

    public function mount()
    {
        $this->cart = Session::get('cart', []);

        // Jika keranjang kosong, balik ke menu
        if (count($this->cart) == 0) {
            return redirect()->route('order');
        }

        // Auto Fill Meja jika dari QR
        if (Session::has('table_number')) {
            $this->table_number = Session::get('table_number');
        }
    }

    // --- LOGIKA KERANJANG (+ / - / Hapus) ---

    public function increaseQty($key)
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['qty']++;
            Session::put('cart', $this->cart);
        }
    }

    public function decreaseQty($key)
    {
        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['qty'] > 1) {
                $this->cart[$key]['qty']--;
            } else {
                unset($this->cart[$key]);
            }
            Session::put('cart', $this->cart);
        }
        if (empty($this->cart)) {
            return redirect()->route('order');
        }
    }

    public function removeItem($key)
    {
        unset($this->cart[$key]);
        Session::put('cart', $this->cart);
        if (empty($this->cart)) {
            return redirect()->route('order');
        }
    }

    public function cancelOrder()
    {
        Session::forget('cart'); 
        return redirect()->route('order'); 
    }

    public function getTotalPriceProperty()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }

    // --- PROSES UTAMA: SUBMIT ORDER ---
    public function submitOrder()
    {
        // 1. Validasi Input
        $rules = [
            'customer_name' => 'required|min:3',
            'table_number'  => 'required|numeric', // Meja bisa auto atau manual
            'payment_method'=> 'required|in:cash,qris',
            'order_type'    => 'required|in:dine_in,take_away',
        ];

        if ($this->payment_method === 'qris') {
            $rules['payment_proof'] = 'required|image|max:1024';
        }

        $this->validate($rules);

        // 2. Cek Stok Terakhir (Safety)
        foreach ($this->cart as $cartItem) {
            $product = Product::find($cartItem['product_id']);
            if (!$product || !$product->is_available) {
                $this->addError('stock_error', "Maaf, stok menu '{$cartItem['name']}' habis. Mohon hapus item tersebut.");
                return; 
            }
        }

        // 3. Upload Bukti (Jika Ada)
        $proofPath = null;
        if ($this->payment_proof) {
            $proofPath = $this->payment_proof->store('payment_proofs', 'public');
        }

        // 4. Simpan Order Utama ke Database
        $order = Order::create([
            'customer_name' => $this->customer_name,
            'table_number'  => $this->table_number,
            'payment_method'=> $this->payment_method,
            'order_type'    => $this->order_type,
            'payment_proof' => $proofPath,
            'total_amount'  => $this->getTotalPriceProperty(),
            'status'        => $this->payment_method === 'cash' ? 'pending' : 'waiting_confirmation',
            'note'          => $this->note, 
        ]);

        // 5. Simpan Detail Item ke Database
        foreach ($this->cart as $cartItem) {
            $optionsString = null;
            if (!empty($cartItem['options'])) {
                $optionsString = implode(', ', array_map(
                    fn ($k, $v) => "$k: $v",
                    array_keys($cartItem['options']),
                    $cartItem['options']
                ));
            }

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $cartItem['product_id'],
                'quantity'   => $cartItem['qty'],
                'unit_price' => $cartItem['price'],
                'options'    => $optionsString, 
            ]);
        }

        // 6. SIMPAN RIWAYAT PESANAN (SESSION) - INI KODE YANG KAMU MINTA
        $history = Session::get('order_history', []);
        $history[] = $order->id; // Masukkan ID order baru ke array history
        Session::put('order_history', $history);

        // 7. Bersihkan Keranjang & Redirect ke Struk
        Session::forget('cart');
        
        session()->flash('success', 'Pesanan berhasil dibuat! Mohon tunggu.');
        return redirect()->route('success', ['id' => $order->id]);
    }

    public function render()
    {
        return view('livewire.checkout-page', ['cartItems' => $this->cart]);
    }
}
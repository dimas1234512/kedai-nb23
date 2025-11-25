<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Session;

class OrderPage extends Component
{
    #[Title('Menu Kedai NB 23')] 

    public $cart = []; 
    public $historyOrders = []; 

    public function mount()
    {
        // Tangkap Meja dari URL
        if (request()->has('table')) {
            $tableNum = request()->query('table');
            if (in_array($tableNum, ['1', '2', '3', '4'])) {
                Session::put('table_number', $tableNum);
            }
        }

        $this->cart = Session::get('cart', []); 

        // Ambil Riwayat Pesanan
        $historyIds = Session::get('order_history', []);
        if (!empty($historyIds)) {
            $this->historyOrders = Order::whereIn('id', $historyIds)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    // --- FUNGSI REORDER (PESAN LAGI) ---
    public function reorder($orderId)
    {
        $oldOrder = Order::with('items.product')->find($orderId);

        if (!$oldOrder) return;

        // Kosongkan keranjang dulu biar gak numpuk
        $this->cart = [];

        foreach ($oldOrder->items as $item) {
            // Cek stok
            if ($item->product && $item->product->is_available) {
                
                // Masukkan produk ke keranjang (Opsi dikosongkan sementara utk MVP)
                $cartId = $item->product_id; 

                $this->cart[$cartId] = [
                    'product_id' => $item->product->id,
                    'name'       => $item->product->name,
                    'price'      => $item->product->price,
                    'image'      => $item->product->image,
                    'qty'        => $item->quantity,
                    'options'    => [] 
                ];
            }
        }

        // Simpan & Update UI
        Session::put('cart', $this->cart);
        
        $summary = $this->getCartSummaryProperty();
        $this->dispatch('update-cart', [
            'total_items' => $summary['total_items'],
            'total_price' => $summary['total_price']
        ]);
    }

    // --- FUNGSI MENERIMA ORDER DARI JS ---
    public function addToCartFromJs($productId, $qty, $options)
    {
        $product = Product::find($productId);

        if (!$product || !$product->is_available) return; 

        $cartId = $product->id;
        if (!empty($options)) {
            ksort($options); 
            $cartId .= '-' . http_build_query($options);
        }

        if (isset($this->cart[$cartId])) {
            $this->cart[$cartId]['qty'] += $qty;
        } else {
            $this->cart[$cartId] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => $product->price,
                'image'      => $product->image,
                'qty'        => $qty,
                'options'    => $options 
            ];
        }

        Session::put('cart', $this->cart);
        
        $summary = $this->getCartSummaryProperty();
        $this->dispatch('update-cart', [
            'total_items' => $summary['total_items'],
            'total_price' => $summary['total_price']
        ]);

        $this->skipRender(); 
    }

    public function getCartSummaryProperty()
    {
        $totalPrice = 0;
        $totalItems = 0;
        foreach ($this->cart as $item) {
            $totalPrice += $item['price'] * $item['qty'];
            $totalItems += $item['qty'];
        }
        return ['total_price' => $totalPrice, 'total_items' => $totalItems];
    }

    public function render()
    {
        $categories = Category::with(['products' => function ($query) {
            $query->orderBy('is_available', 'desc'); 
        }])->get();

        return view('livewire.order-page', [
            'categories' => $categories,
            'summary' => $this->getCartSummaryProperty()
        ]);
    }
}
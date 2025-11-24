<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Session;

class OrderPage extends Component
{
    #[Title('Menu Kedai NB 23')] 

    public $cart = []; 

    public function mount()
    {
        $this->cart = Session::get('cart', []); 
    }

    // --- FUNGSI MENERIMA ORDER (SILENT MODE) ---
    public function addToCartFromJs($productId, $qty, $options)
    {
        $product = Product::find($productId);

        if (!$product || !$product->is_available) {
            return; 
        }

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
        
        // Hitung Total Terbaru
        $summary = $this->getCartSummaryProperty();

        // 1. Kirim data terbaru ke Javascript (Event Listener)
        $this->dispatch('update-cart', [
            'total_items' => $summary['total_items'],
            'total_price' => $summary['total_price']
        ]);

        // 2. KUNCI RAHASIA: JANGAN REFRESH HALAMAN!
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
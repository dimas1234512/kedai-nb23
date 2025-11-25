<div class="max-w-md mx-auto bg-gray-50 min-h-screen pb-32 font-sans relative overflow-hidden" 
     x-data="{
         ...orderSystem(
             {{ $categories->first()->id ?? 0 }}, 
             {{ $summary['total_items'] ?? 0 }}, 
             {{ $summary['total_price'] ?? 0 }}
         ),
         historyOpen: false,
         selectedHistory: null
     }">
    
    <div class="bg-white shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] sticky top-0 z-30 rounded-b-[2rem]">
        <div class="px-6 pt-6 pb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-yellow-400 p-0.5 shadow-sm">
                    <img src="https://cdn-icons-png.flaticon.com/512/1046/1046784.png" class="w-full h-full object-cover rounded-full bg-white">
                </div>
                <div>
                    <h1 class="text-xl font-black text-gray-900 leading-none tracking-tight">
                        KEDAI <span class="text-yellow-500">NB'23</span>
                    </h1>
                    <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase mt-0.5">Open Now</p>
                </div>
            </div>

            @if(count($historyOrders) > 0)
                <button @click="historyOpen = true" 
                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-full hover:bg-yellow-100 hover:text-yellow-800 transition flex items-center gap-2 border border-gray-200 shadow-sm active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-bold">Riwayat Pesanan</span>
                </button>
            @endif
        </div>

        <div class="flex overflow-x-auto whitespace-nowrap gap-2 px-6 pb-4 pt-1 scrollbar-hide">
            @foreach ($categories as $category)
                <button @click="activeTab = {{ $category->id }}"
                    class="px-5 py-2 text-sm font-bold rounded-full transition-all duration-200 border"
                    :class="activeTab === {{ $category->id }} 
                        ? 'bg-yellow-400 text-black border-yellow-400 shadow-sm transform scale-105' 
                        : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="px-5 mt-6 min-h-[50vh]">
        @foreach ($categories as $category)
            <div x-show="activeTab === {{ $category->id }}" style="display: none;">
                <div class="flex items-center gap-2 mb-4 px-1">
                    <h2 class="text-lg font-black text-gray-800 uppercase tracking-wide">{{ $category->name }}</h2>
                    <div class="h-px bg-gray-200 flex-1"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @forelse ($category->products as $product)
                        <div @click="openProduct({{ Js::from($product) }})" 
                             class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 cursor-pointer active:scale-95 transition-all duration-200 hover:shadow-md group relative h-full flex flex-col
                             {{ !$product->is_available ? 'opacity-60 pointer-events-none' : '' }}">
                            
                            <div class="aspect-square w-full bg-gray-50 rounded-xl overflow-hidden relative mb-3">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif

                                @if(!$product->is_available)
                                    <div class="absolute inset-0 bg-white/60 flex items-center justify-center backdrop-blur-[1px]">
                                        <span class="bg-gray-800 text-white text-[10px] font-bold px-2 py-1 rounded uppercase">Habis</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 flex flex-col justify-between">
                                <h3 class="text-sm font-bold text-gray-800 leading-snug line-clamp-2 mb-2">{{ $product->name }}</h3>
                                <div class="flex items-center justify-between mt-auto">
                                    <p class="text-gray-900 font-extrabold text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    @if($product->is_available)
                                        <div class="w-7 h-7 rounded-full bg-yellow-400 text-black flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 py-12 text-center text-gray-400 text-sm">Menu belum tersedia.</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <div x-show="modalOpen" 
         class="fixed inset-0 z-[60] flex justify-center items-end pointer-events-none"
         style="display: none;">
         
         <div class="w-full max-w-md h-full relative flex flex-col justify-end pointer-events-auto">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                 @click="closeModal()"
                 x-show="modalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div class="bg-white w-full rounded-t-[2.5rem] overflow-hidden shadow-[0_-10px_60px_rgba(0,0,0,0.3)] relative z-10 flex flex-col max-h-[90vh]"
                 x-show="modalOpen"
                 x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                
                <div class="relative h-72 w-full flex-shrink-0 bg-gray-100">
                    <img :src="activeProduct.image ? '/storage/' + activeProduct.image : ''" class="w-full h-full object-cover" x-show="activeProduct.image">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <button @click="closeModal()" class="absolute top-5 right-5 bg-white/20 backdrop-blur-md border border-white/30 p-2 rounded-full text-white shadow-lg hover:bg-white hover:text-black transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <div class="absolute bottom-6 left-6 right-6">
                        <h2 class="text-3xl font-black text-white leading-tight shadow-black drop-shadow-md" x-text="activeProduct.name"></h2>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto flex-1 bg-white relative -mt-4 rounded-t-[2rem] z-20">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-2xl font-bold text-yellow-600">
                            Rp <span x-text="formatRupiah(activeProduct.price)"></span>
                        </span>
                        <template x-if="activeProduct.is_available">
                            <span class="text-xs font-bold bg-green-100 text-green-700 px-2 py-1 rounded">Tersedia</span>
                        </template>
                        <template x-if="!activeProduct.is_available">
                            <span class="text-xs font-bold bg-red-100 text-red-700 px-2 py-1 rounded">Habis</span>
                        </template>
                    </div>
                    
                    <p class="text-gray-500 text-sm leading-relaxed mb-6" x-text="activeProduct.description"></p>
                    <hr class="border-gray-100 mb-6">

                    <template x-if="activeProduct.options">
                        <div class="space-y-6">
                            <template x-for="option in activeProduct.options" :key="option.name">
                                <div>
                                    <h3 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider mb-3" x-text="option.name"></h3>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="choice in option.choices">
                                            <label class="relative" :class="choice.is_available ? 'cursor-pointer' : 'cursor-not-allowed opacity-50 grayscale'">
                                                <input type="radio" :name="option.name" :value="choice.name" @change="selectedOptions[option.name] = choice.name" :checked="selectedOptions[option.name] === choice.name" class="peer hidden" :disabled="!choice.is_available">
                                                <span class="px-5 py-2.5 rounded-xl border-2 border-gray-100 text-sm font-bold text-gray-500 bg-white transition-all peer-checked:border-yellow-400 peer-checked:bg-yellow-50 peer-checked:text-yellow-900 hover:border-gray-300 select-none block shadow-sm">
                                                    <span x-text="choice.name"></span>
                                                </span>
                                                <template x-if="!choice.is_available">
                                                    <span class="absolute -top-2 -right-1 bg-black text-white text-[8px] px-2 py-0.5 rounded-full font-bold shadow-sm z-10">HABIS</span>
                                                </template>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="p-5 border-t border-gray-100 bg-white pb-8">
                    <template x-if="activeProduct.is_available">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-3 bg-gray-100 rounded-2xl p-2 border border-gray-200">
                                <button @click="if(qty > 1) qty--" class="w-10 h-10 rounded-xl bg-white shadow-sm text-xl font-bold text-black hover:bg-gray-100 flex items-center justify-center transition disabled:opacity-50">-</button>
                                <span class="font-black text-xl w-8 text-center" x-text="qty">1</span>
                                <button @click="qty++" class="w-10 h-10 rounded-xl bg-black shadow-sm text-xl font-bold text-white hover:bg-gray-800 flex items-center justify-center transition">+</button>
                            </div>
                            <button @click="submitToCart" class="flex-1 bg-yellow-400 text-black font-black py-4 rounded-2xl shadow-lg hover:bg-yellow-300 active:scale-[0.98] transition flex flex-col items-center justify-center leading-none">
                                <span class="text-sm">TAMBAH PESANAN</span>
                                <span class="text-[10px] opacity-80 mt-0.5 font-bold">
                                    Rp <span x-text="formatRupiah(activeProduct.price * qty)"></span>
                                </span>
                            </button>
                        </div>
                    </template>
                    <template x-if="!activeProduct.is_available">
                        <button disabled class="w-full bg-gray-200 text-gray-400 font-bold py-4 rounded-2xl cursor-not-allowed flex items-center justify-center gap-2 border-2 border-dashed border-gray-300">
                            <span>MAAF, STOK HABIS</span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div x-show="historyOpen" 
         class="fixed inset-0 z-[70] flex items-end justify-center sm:items-center p-0 sm:p-4" 
         x-data="{ selectedHistory: null }" 
         style="display: none;">
        
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="historyOpen = false; selectedHistory = null"></div>

        <div class="bg-gray-100 w-full max-w-md h-[85vh] rounded-t-[2rem] sm:rounded-3xl overflow-hidden flex flex-col shadow-2xl relative z-20 transform transition-transform"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">

            <div class="px-6 py-5 bg-white shadow-sm z-10 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button x-show="selectedHistory" @click="selectedHistory = null" class="p-1 rounded-full hover:bg-gray-100 mr-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    
                    <div>
                        <h2 class="text-xl font-black text-gray-900" x-text="selectedHistory ? 'Detail Pesanan' : 'Riwayat'"></h2>
                        <p class="text-xs text-gray-400 font-medium" x-text="selectedHistory ? 'Rincian menu yang dipesan' : 'Daftar pesanan kamu sebelumnya'"></p>
                    </div>
                </div>
                
                <button @click="historyOpen = false; selectedHistory = null" class="p-2 bg-gray-50 rounded-full hover:bg-gray-100 transition border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-4" x-show="!selectedHistory" x-transition:enter="transition ease-out duration-200">
                @forelse ($historyOrders as $history)
                    <div @click="selectedHistory = {{ $history->id }}" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 relative overflow-hidden cursor-pointer active:scale-95 transition hover:shadow-md">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $history->status == 'paid' ? 'bg-green-500' : ($history->status == 'cancelled' ? 'bg-red-500' : 'bg-yellow-500') }}"></div>

                        <div class="flex justify-between items-start mb-2 pl-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gray-100 p-1"><img src="https://cdn-icons-png.flaticon.com/512/1046/1046784.png" class="w-full h-full object-cover"></div>
                                    <h3 class="text-sm font-bold text-gray-900">Kedai NB'23</h3>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 ml-8">{{ $history->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </div>

                        <div class="pl-3 flex justify-between items-end mt-4">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Total</p>
                                <p class="text-sm font-black text-gray-900">Rp {{ number_format($history->total_amount, 0, ',', '.') }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase {{ $history->status == 'paid' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                {{ $history->status == 'paid' ? 'Selesai' : 'Proses' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 text-gray-400">Belum ada riwayat.</div>
                @endforelse
            </div>

            @foreach ($historyOrders as $history)
                <div class="flex-1 overflow-y-auto p-5 bg-white relative" 
                     x-show="selectedHistory === {{ $history->id }}" 
                     style="display: none;"
                     x-transition:enter="transition ease-out duration-300 translate-x-full"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0">
                    
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-3 {{ $history->status == 'paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                            @if($history->status == 'paid')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @endif
                        </div>
                        <h3 class="text-lg font-black text-gray-900">{{ $history->status == 'paid' ? 'Pesanan Selesai' : 'Dalam Proses' }}</h3>
                        <p class="text-xs text-gray-500">ID Order: #{{ $history->id }}</p>
                        
                        <div class="mt-3 flex justify-center gap-2">
                            <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-3 py-1 rounded-full border border-gray-200">
                                Meja No. {{ $history->table_number }}
                            </span>
                            <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-3 py-1 rounded-full border border-blue-100">
                                {{ $history->order_type == 'take_away' ? 'Take Away' : 'Dine In' }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-4 text-center">
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Pemesan</p>
                        <p class="text-sm font-bold text-gray-900">{{ $history->customer_name }}</p>
                    </div>

                    @if($history->note)
                        <div class="mb-5 bg-yellow-50 p-3 rounded-xl border border-yellow-100 text-left">
                            <p class="text-[10px] font-bold text-yellow-600 uppercase tracking-wider mb-1">Catatan:</p>
                            <p class="text-sm text-gray-800 italic leading-relaxed">"{{ $history->note }}"</p>
                        </div>
                    @endif

                    <div class="space-y-4 mb-8">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b pb-2">Rincian Menu</p>
                        @foreach ($history->items as $item)
                            <div class="flex justify-between items-start">
                                <div class="flex gap-3">
                                    <div class="font-bold text-gray-900 text-sm bg-gray-100 w-6 h-6 flex items-center justify-center rounded">{{ $item->quantity }}x</div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $item->product->name }}</p>
                                        @if($item->options)
                                            <p class="text-[10px] text-gray-500 italic">{{ $item->options }}</p>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm font-bold text-gray-900">Rp {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-4 mt-auto">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-bold text-gray-500">Metode Bayar</span>
                            <span class="text-sm font-bold text-gray-900 uppercase bg-gray-100 px-2 rounded text-xs">{{ $history->payment_method }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm font-bold text-gray-500">Total Bayar</span>
                            <span class="text-xl font-black text-gray-900">Rp {{ number_format($history->total_amount, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('success', $history->id) }}" class="py-3 rounded-xl border border-gray-200 text-center text-sm font-bold text-gray-600 hover:bg-gray-50">
                                Lihat Struk
                            </a>
                            <button wire:click="reorder({{ $history->id }})" @click="historyOpen = false" class="py-3 rounded-xl bg-orange-500 text-center text-sm font-bold text-white hover:bg-orange-600 shadow-lg active:scale-95 transition">
                                Pesan Lagi
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="fixed bottom-6 left-6 right-6 z-40 max-w-md mx-auto animate-bounce-in"
         x-show="cart.total_items > 0" x-cloak style="display: none;">
        <a href="{{ route('checkout') }}" class="bg-gray-900 text-white p-3 pl-5 pr-3 rounded-full shadow-2xl flex items-center justify-between hover:bg-black transition-all active:scale-[0.98] border-2 border-gray-800/50">
            <div class="flex items-center gap-4">
                <div class="bg-yellow-400 text-black font-black w-8 h-8 flex items-center justify-center rounded-full">
                    <span x-text="cart.total_items"></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Total Bayar</span>
                    <span class="font-bold text-lg leading-none text-white">
                        Rp <span x-text="formatRupiah(cart.total_price)"></span>
                    </span>
                </div>
            </div>
            <div class="bg-white text-black px-5 py-2.5 rounded-full font-bold text-sm flex items-center gap-2">
                Checkout
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </div>
        </a>
    </div>

    <script>
        function orderSystem(defaultTab, initialItems, initialPrice) {
            return {
                activeTab: defaultTab,
                modalOpen: false,
                activeProduct: {}, 
                qty: 1,
                selectedOptions: {},
                cart: { total_items: initialItems, total_price: initialPrice },

                init() {
                    Livewire.on('update-cart', (data) => {
                        const payload = data[0]; 
                        this.cart.total_items = payload.total_items;
                        this.cart.total_price = payload.total_price;
                    });
                },

                formatRupiah(angka) { return new Intl.NumberFormat('id-ID').format(angka); },

                openProduct(product) {
                    this.activeProduct = product;
                    this.qty = 1;
                    this.selectedOptions = {};
                    if (product.options) {
                        product.options.forEach(option => {
                            if (option.choices) {
                                for (let choice of option.choices) {
                                    let isAvailable = (choice.is_available === undefined) ? true : choice.is_available;
                                    if (isAvailable) {
                                        this.selectedOptions[option.name] = choice.name;
                                        break; 
                                    }
                                }
                            }
                        });
                    }
                    this.modalOpen = true;
                },

                closeModal() { this.modalOpen = false; },

                submitToCart() {
                    let addedPrice = this.activeProduct.price * this.qty;
                    this.cart.total_items += this.qty;
                    this.cart.total_price += addedPrice;
                    this.modalOpen = false;
                    @this.addToCartFromJs(this.activeProduct.id, this.qty, this.selectedOptions);
                }
            }
        }
    </script>
</div>
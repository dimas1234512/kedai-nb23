<div class="max-w-md mx-auto bg-white min-h-screen font-sans pb-10">
    
    <div class="bg-white p-4 shadow-sm flex items-center gap-3 sticky top-0 z-10">
        <a href="{{ route('order') }}" class="text-gray-600 hover:text-black transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-lg font-bold text-gray-800">Konfirmasi Pesanan</h1>
    </div>

    <div class="p-4 space-y-6">
        
        <section>
            <h2 class="font-bold text-gray-800 mb-2">Menu yang Dipesan</h2>
            <div class="bg-gray-50 rounded-xl p-3 space-y-3">
                @foreach ($cart as $key => $item)
                    <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4 last:border-0 last:mb-0 animate-fade-in">
                        
                        <div class="flex gap-3 overflow-hidden flex-1">
                            <div class="w-14 h-14 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0 border border-gray-200">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0 flex flex-col justify-center">
                                <h3 class="text-sm font-bold text-gray-800 truncate leading-tight">{{ $item['name'] }}</h3>
                                @if(!empty($item['options']))
                                    <p class="text-[10px] text-gray-500 truncate mt-0.5">
                                        @foreach($item['options'] as $optName => $optValue)
                                            {{ $optValue }} @if(!$loop->last) , @endif
                                        @endforeach
                                    </p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <p class="text-sm font-black text-gray-900">
                                Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                            </p>
                            
                            <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200 h-8 shadow-sm">
                                <button type="button" wire:click="decreaseQty('{{ $key }}')" class="w-8 h-full flex items-center justify-center text-gray-500 hover:bg-gray-200 hover:text-red-500 transition rounded-l-lg active:bg-gray-300">
                                    @if($item['qty'] > 1) 
                                        <span class="text-lg font-bold mb-0.5">-</span> 
                                    @else 
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg> 
                                    @endif
                                </button>
                                
                                <div class="w-8 text-center text-sm font-bold text-gray-900 bg-white h-full flex items-center justify-center border-x border-gray-200">
                                    {{ $item['qty'] }}
                                </div>
                                
                                <button type="button" wire:click="increaseQty('{{ $key }}')" class="w-8 h-full flex items-center justify-center text-gray-500 hover:bg-gray-200 hover:text-green-600 transition rounded-r-lg active:bg-gray-300">
                                    <span class="text-lg font-bold mb-0.5">+</span>
                                </button>
                            </div>
                            
                            </div>
                    </div>
                @endforeach

                <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between items-center">
                    <span class="font-bold text-gray-800">Total Bayar</span>
                    <span class="font-bold text-xl text-yellow-600">Rp {{ number_format($this->totalPrice, 0, ',', '.') }}</span>
                </div>
            </div>
        </section>

        <form wire:submit="submitOrder" class="space-y-5">
            
            <section>
                <h2 class="font-bold text-gray-800 mb-2">Mau Makan Dimana?</h2>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="order_type" value="dine_in" class="peer hidden">
                        <div class="p-3 border rounded-xl text-center peer-checked:border-yellow-400 peer-checked:bg-yellow-50 transition flex flex-col items-center gap-1 justify-center h-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 peer-checked:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            <span class="block font-bold text-gray-700 text-sm">Dine In</span>
                            <span class="text-[10px] text-gray-500">Makan di sini</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="order_type" value="take_away" class="peer hidden">
                        <div class="p-3 border rounded-xl text-center peer-checked:border-yellow-400 peer-checked:bg-yellow-50 transition flex flex-col items-center gap-1 justify-center h-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 peer-checked:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                            <span class="block font-bold text-gray-700 text-sm">Take Away</span>
                            <span class="text-[10px] text-gray-500">Dibungkus</span>
                        </div>
                    </label>
                </div>
            </section>

            <section>
                <h2 class="font-bold text-gray-800 mb-2">Data Pemesan</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Nama Pemesan</label>
                        <input type="text" wire:model="customer_name" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400" placeholder="Masukkan nama kamu...">
                        @error('customer_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Nomor Meja</label>
                        <input type="number" wire:model="table_number" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400" placeholder="Contoh: 5">
                        @error('table_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Catatan Pesanan (Opsional)</label>
                        <textarea wire:model="note" rows="2" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400" placeholder="Contoh: Jangan pedas, Es batu sedikit..."></textarea>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="font-bold text-gray-800 mb-2">Metode Pembayaran</h2>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="payment_method" value="cash" class="peer hidden">
                        <div class="p-3 border rounded-xl text-center peer-checked:border-yellow-400 peer-checked:bg-yellow-50 transition">
                            <span class="block font-bold text-gray-700">Tunai / Cash</span>
                            <span class="text-xs text-gray-500">Bayar di kasir</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="payment_method" value="qris" class="peer hidden">
                        <div class="p-3 border rounded-xl text-center peer-checked:border-yellow-400 peer-checked:bg-yellow-50 transition">
                            <span class="block font-bold text-gray-700">QRIS</span>
                            <span class="text-xs text-gray-500">Scan & Upload</span>
                        </div>
                    </label>
                </div>
            </section>

            @if($payment_method === 'qris')
                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl text-center animate-fade-in-down">
                    <p class="text-sm font-bold text-gray-800 mb-2">Scan QRIS Kedai NB'23</p>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg" class="w-32 h-32 mx-auto mb-3 bg-white p-2 rounded-lg">
                    <label class="block text-left text-xs font-bold text-gray-600 mb-1">Upload Bukti Bayar</label>
                    <input type="file" wire:model="payment_proof" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-400 file:text-black hover:file:bg-yellow-500">
                    @error('payment_proof') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="payment_proof" class="text-xs text-gray-500 mt-2">Mengupload foto...</div>
                </div>
            @endif

            @error('stock_error')
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-3 text-sm">
                    <span class="block sm:inline">{{ $message }}</span>
                </div>
            @enderror

            <div class="pt-2 space-y-3">
                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-gray-800 transition active:scale-95">
                    PESAN SEKARANG
                </button>

                <button type="button" 
                        wire:click="cancelOrder" 
                        wire:confirm="Yakin ingin membatalkan pesanan? Keranjang akan dikosongkan."
                        class="w-full py-3 text-sm font-bold text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl transition">
                    Batalkan Pesanan & Hapus Keranjang
                </button>
            </div>
        </form>
    </div>
</div>
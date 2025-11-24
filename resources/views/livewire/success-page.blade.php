<div class="max-w-md mx-auto bg-gray-100 min-h-screen p-6 flex flex-col justify-center font-sans">
    
    <div class="bg-white p-6 rounded-xl shadow-lg relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-2 bg-yellow-400"></div>

        <div class="text-center mb-6 border-b border-dashed border-gray-300 pb-6">
            <div class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-xl font-black text-gray-900">Pesanan Berhasil!</h1>
            <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
            <p class="text-xs text-gray-400">ID: #{{ $order->id }}</p>

            <div class="mt-2 flex justify-center">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border {{ $order->order_type == 'take_away' ? 'bg-black text-white border-black' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                    {{ $order->order_type == 'take_away' ? 'Dibungkus / Take Away' : 'Makan Ditempat' }}
                </span>
            </div>
        </div>

        <div class="flex justify-between mb-4 text-sm">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Pemesan</p>
                <p class="font-bold text-gray-800">{{ $order->customer_name }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase tracking-wider">Meja</p>
                <p class="font-bold text-gray-800 text-lg">No. {{ $order->table_number }}</p>
            </div>
        </div>

        <div class="space-y-3 mb-6">
            @foreach ($order->items as $item)
                <div class="flex justify-between text-sm items-start">
                    <div class="pr-2">
                        <span class="font-bold text-gray-800">{{ $item->quantity }}x</span> 
                        <span class="text-gray-700">{{ $item->product->name }}</span>
                        @if($item->options)
                            <p class="text-[10px] text-gray-500 italic mt-0.5 leading-tight">{{ $item->options }}</p>
                        @endif
                    </div>
                    <span class="font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        @if($order->note)
            <div class="mb-6 bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                <p class="text-[10px] text-yellow-600 uppercase font-bold mb-1">Catatan:</p>
                <p class="text-sm text-gray-800 italic">"{{ $order->note }}"</p>
            </div>
        @endif

        <div class="border-t-2 border-gray-100 pt-4 mb-6">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-500 text-sm">Metode Bayar</span>
                <span class="font-bold text-gray-800 uppercase bg-gray-100 px-2 py-1 rounded text-xs">{{ $order->payment_method }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-bold text-lg text-gray-900">Total Bayar</span>
                <span class="font-black text-xl text-yellow-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-gray-900 text-white text-xs p-3 rounded-xl text-center font-medium mb-6">
            @if($order->payment_method == 'cash')
                Silakan menuju kasir untuk melakukan pembayaran.
            @else
                Pembayaran QRIS sedang diverifikasi kasir.
            @endif
        </div>

        <div class="border-t border-dashed border-gray-300 pt-5 text-center">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Akses Internet Gratis</p>
            
            <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-4 relative group overflow-hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 absolute -right-6 -bottom-6 text-gray-200 transform rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                </svg>

                <div class="relative z-10">
                    <div class="flex justify-center items-center gap-2 mb-1 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" /></svg>
                        <span class="text-sm font-medium">Wi-Fi Kedai NB</span>
                    </div>
                    
                    <h2 class="text-2xl font-black text-gray-900 tracking-wider select-all">
                        kopienak123
                    </h2>
                    <p class="text-[10px] text-gray-400 mt-1">Password</p>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-6 space-y-3 print:hidden">
        <button onclick="window.print()" class="w-full bg-white border border-gray-300 text-gray-700 font-bold py-3.5 rounded-xl shadow-sm hover:bg-gray-50 transition flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Simpan Struk
        </button>
        
        <a href="{{ route('order') }}" class="block w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl shadow-lg hover:bg-gray-800 transition text-center">
            Pesan Lagi
        </a>
    </div>
</div>
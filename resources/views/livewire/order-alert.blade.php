<div wire:poll.10s="checkOrders">
    {{-- Cek setiap 5 detik --}}

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('play-sound', () => {
                // Buat objek audio baru langsung dari file public
                let audio = new Audio('/notif.mp3');
                
                // Coba mainkan
                audio.play().catch(error => {
                    console.log("Browser memblokir suara: Silakan interaksi (klik) di halaman dulu.");
                });
            });
        });
    </script>
</div>
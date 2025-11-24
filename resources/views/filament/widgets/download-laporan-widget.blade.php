<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                Laporan Keuangan
            </h2>
        </div>

        {{-- Form Tanggal --}}
        <form wire:submit="download">
            {{ $this->form }}
            
            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit" icon="heroicon-m-arrow-down-tray" color="success">
                    Download Excel
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use App\Exports\IncomeExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class DownloadLaporanWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.download-laporan-widget';
    
    protected static ?int $sort = 2; 
    protected int | string | array $columnSpan = 'full';

    // Data form disimpan di sini
    public ?array $data = [];

    public function mount(): void
    {
        // Set default tanggal
        $this->form->fill([
            'startDate' => Carbon::now()->startOfMonth(),
            'endDate' => Carbon::now(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Download Laporan Keuangan')
                    ->description('Pilih rentang tanggal untuk mengunduh laporan pemasukan (Excel).')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Dari Tanggal')
                            ->required()
                            ->maxDate(now()),
                        
                        DatePicker::make('endDate')
                            ->label('Sampai Tanggal')
                            ->required()
                            ->maxDate(now()),
                    ])
                    ->columns(2)
                    ->statePath('data'), // Ini menyambungkan form ke variabel $data di atas
            ]);
    }

    public function download()
    {
        // 1. Validasi dulu (biar error kalau tanggal kosong)
        $this->form->validate();

        // 2. Ambil data LANGSUNG dari properti $this->data (PERBAIKAN DISINI)
        $formValues = $this->data;
        
        $start = Carbon::parse($formValues['startDate'])->format('Y-m-d');
        $end = Carbon::parse($formValues['endDate'])->format('Y-m-d');

        // 3. Download Excel
        return Excel::download(new IncomeExport($start, $end), 'Laporan-Pemasukan-' . $start . '-sampai-' . $end . '.xlsx');
    }
}
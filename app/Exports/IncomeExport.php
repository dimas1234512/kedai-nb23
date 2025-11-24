<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class IncomeExport implements FromQuery, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    use Exportable;

    public $startDate;
    public $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        return Order::query()
            ->with('items.product')
            ->where('status', 'paid')
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID Order',
            'Tanggal & Jam',
            'Nama Pelanggan',
            'Menu yang Dipesan',
            'Status',
            'Metode Bayar',
            'Total Harga',
        ];
    }

    public function map($order): array
    {
        // Format Menu
        $menuList = $order->items->map(function ($item) {
            $variant = $item->options ? " [{$item->options}]" : "";
            return "- {$item->product->name} ({$item->quantity}x){$variant}";
        })->implode("\n");

        // Format Status
        $statusLabel = match($order->status) {
            'paid' => 'Lunas',
            default => $order->status,
        };

        return [
            '#' . $order->id,
            $order->created_at->format('d/m/Y H:i'),
            $order->customer_name,
            $menuList,
            $statusLabel,
            strtoupper($order->payment_method),
            (float) $order->total_amount, // Pastikan ini angka murni
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow(); 
                $totalRow = $highestRow + 1;

                // --- PERBAIKAN UTAMA: HITUNG TOTAL DI PHP ---
                // Kita hitung manual totalnya pakai Query Database agar 100% Akurat
                $grandTotal = Order::where('status', 'paid')
                    ->whereDate('created_at', '>=', $this->startDate)
                    ->whereDate('created_at', '<=', $this->endDate)
                    ->sum('total_amount');

                // Tulis Label & Angka Hasil Hitungan
                $sheet->setCellValue('F' . $totalRow, 'TOTAL PENDAPATAN');
                $sheet->setCellValue('G' . $totalRow, $grandTotal); // Masukkan Angka Langsung (Bukan Rumus)

                // --- STYLING (Sama seperti sebelumnya) ---
                $sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FACC15'], 
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Border & Alignment Data
                $dataRange = 'A1:G' . $totalRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP, 
                    ],
                ]);

                // Wrap Text Menu
                $sheet->getStyle('D2:D' . $highestRow)->getAlignment()->setWrapText(true);
                $sheet->getColumnDimension('D')->setWidth(50); 

                // Format Angka Rupiah (Kolom G sampai Bawah)
                $sheet->getStyle('G2:G' . $totalRow)
                      ->getNumberFormat()
                      ->setFormatCode('#,##0');

                // Styling Baris Total
                $sheet->getStyle('F' . $totalRow . ':G' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'EEEEEE'], 
                    ],
                ]);
            },
        ];
    }
}
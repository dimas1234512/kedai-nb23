<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'Pesanan Masuk';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->orWhere('status', 'waiting_confirmation')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // --- BAGIAN FORM DETAIL ---
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Pemesan')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')->label('Nama Pemesan')->readOnly(),
                        Forms\Components\TextInput::make('table_number')->label('Nomor Meja')->readOnly(),
                        
                        // Tampilkan Tipe Pesanan di Form Detail juga (Readonly)
                        Forms\Components\TextInput::make('order_type')
                            ->label('Tipe Pesanan')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'dine_in' => 'Makan Ditempat',
                                'take_away' => 'Bungkus',
                                default => $state,
                            })
                            ->readOnly(),

                        Forms\Components\TextInput::make('total_amount')->label('Total Harga')->prefix('Rp')->numeric()->readOnly(),
                        
                        Forms\Components\Textarea::make('note')
                            ->label('Catatan Pelanggan')
                            ->columnSpanFull()
                            ->readOnly(),

                        Forms\Components\Select::make('status')
                            ->label('Update Status Pesanan')
                            ->options([
                                'pending' => 'Baru (Cash)',
                                'waiting_confirmation' => 'Baru (Cek QRIS)',
                                'paid' => 'Selesai / Lunas',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Bukti Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('payment_method')->label('Metode Bayar')->readOnly(),
                        Forms\Components\FileUpload::make('payment_proof')->label('Foto Bukti Transfer')->image()->directory('payment_proofs')->openable()->downloadable()
                            ->visible(fn (Get $get) => $get('payment_method') === 'qris'), 
                    ])->collapsible(),

                Section::make('Daftar Pesanan')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')->label('Menu')->options(Product::all()->pluck('name', 'id'))->disabled()->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')->label('Qty')->disabled()->columnSpan(1),
                                Forms\Components\TextInput::make('unit_price')->label('Harga @')->disabled()->columnSpan(1),
                                Forms\Components\TextInput::make('options')->label('Varian / Opsi')->disabled()->columnSpanFull(),
                            ])->disableItemCreation()->disableItemDeletion()->columns(4),
                    ]),
            ]);
    }

    // --- BAGIAN TABEL UTAMA ---
    public static function table(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->columns([
                // 1. Waktu
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('H:i')
                    ->description(fn (Order $record): string => $record->created_at->format('d M Y'))
                    ->sortable()
                    ->alignCenter(),

                // 2. Pelanggan
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Pelanggan')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn (Order $record): string => $record->note ? '📝 ' . $record->note : ''),

                // 3. TIPE PESANAN (BARU DITAMBAHKAN)
                Tables\Columns\TextColumn::make('order_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dine_in' => 'info',    // Biru
                        'take_away' => 'warning', // Kuning
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'dine_in' => 'Makan Ditempat',
                        'take_away' => 'Bungkus',
                    })
                    ->alignCenter(),

                // 4. Nomor Meja
                Tables\Columns\TextColumn::make('table_number')
                    ->label('Meja')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => "#{$state}"),

                // 5. Menu Dipesan
                Tables\Columns\TextColumn::make('items_summary')
                    ->label('Menu Dipesan')
                    ->getStateUsing(function (Order $record) {
                        return $record->items->map(function ($item) {
                            $variant = $item->options ? " <span class='text-gray-400 text-xs'>({$item->options})</span>" : "";
                            return "{$item->quantity}x <strong>{$item->product->name}</strong>{$variant}";
                        })->implode('<br>');
                    })
                    ->html()
                    ->wrap(),

                // 6. Metode Bayar
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Via')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'cash' => 'heroicon-o-banknotes',
                        'qris' => 'heroicon-o-qr-code',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'qris' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->alignCenter(),

                // 7. Total Harga
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->weight('bold')
                    ->color('warning'),
                
                // 8. Status Dropdown
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => '⏳ Baru (Cash)', 
                        'waiting_confirmation' => '📱 Cek QRIS',
                        'paid' => '✅ SELESAI', 
                        'cancelled' => '❌ BATAL',
                    ])
                    ->selectablePlaceholder(false)
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    })
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Download Excel')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename(fn ($resource) => 'Laporan-KedaiNB-' . date('Y-m-d'))
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->iconButton()
                    ->tooltip('Lihat Detail Lengkap'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(), 
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['items.product']); // Ambil data items & produk sekaligus
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
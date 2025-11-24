<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set; 
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder; // <--- INI YANG TADI KURANG

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationLabel = 'List Menu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Kotak 1: Pilih Kategori
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori')
                    ->required(),
                
                // Kotak 2: Nama Menu (Auto Slug)
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                
                // Kotak 3: Harga
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                // Kotak 4: Upload Foto (Max 2MB)
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('products')
                    ->maxSize(2048) 
                    ->columnSpanFull(), 

                // Kotak 5: Deskripsi Menu
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Menu')
                    ->rows(3)
                    ->columnSpanFull(),

                // --- FITUR BARU: OPSI DENGAN STOK ---
                Forms\Components\Repeater::make('options')
                    ->label('Opsi / Varian Menu')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Opsi (Contoh: Rasa / Level)')
                            ->required(),
                        
                        // Nested Repeater untuk Pilihan + Stok
                        Forms\Components\Repeater::make('choices')
                            ->label('Daftar Pilihan')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Pilihan (Cth: Coklat)')
                                    ->required(),
                                
                                Forms\Components\Toggle::make('is_available')
                                    ->label('Ada?')
                                    ->default(true)
                            ])
                            ->grid(2) // Tampil berjejer
                            ->defaultItems(1)
                    ])
                    ->columnSpanFull(),
                // ------------------------------------

                // Kotak 7: Stok Utama Tersedia?
                Forms\Components\Toggle::make('is_available')
                    ->label('Stok Menu Utama Tersedia')
                    ->default(true),
                    
                // Kotak 8: Slug
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Foto'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold')
                    ->description(fn (Product $record): string => Str::limit($record->description ?? '', 30)),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->sortable()->badge(),
                Tables\Columns\TextColumn::make('price')->money('IDR')->sortable(),
                Tables\Columns\ToggleColumn::make('is_available')->label('Stok Ready?'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name')->label('Filter Kategori'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Optimasi Query N+1 (Biar Admin Ngebut)
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['category']); 
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
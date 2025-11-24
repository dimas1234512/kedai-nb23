<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set; // Tambahan untuk auto-slug
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str; // Tambahan untuk auto-slug

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag'; // Saya ganti ikon jadi Tag biar beda
    
    protected static ?string $navigationLabel = 'Kategori Menu';

    // Bagian FORM (Input Data)
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Input Nama Kategori (Ada Auto-Slug nya)
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true) // Saat selesai ketik, jalankan kode bawah
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    
                // Input Slug (Link) - Terisi otomatis
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->readOnly() // Biar tidak diubah manual sembarangan
                    ->maxLength(255),
            ]);
    }

    // Bagian TABLE (Daftar Data)
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug Link'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // INI YANG TADI HILANG (Tombol Edit & Hapus)
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
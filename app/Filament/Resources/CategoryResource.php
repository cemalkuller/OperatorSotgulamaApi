<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationGroup = 'Finans';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Kategoriler'; // Menüde gözüken isim
    protected static ?string $pluralModelLabel = 'Kategoriler'; // Breadcrumb ve başlıklar için
    protected static ?string $modelLabel = 'Kategori'; // Tekil başlık

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Kategori Adı')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('type')
                    ->label('Tür')
                    ->options([
                        'income' => 'Gelir',
                        'expense' => 'Gider',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Kategori Adı')->searchable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tür')
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ])
                    ->formatStateUsing(fn($state) => $state === 'income' ? 'Gelir' : 'Gider'),
                Tables\Columns\TextColumn::make('created_at')->label('Oluşturulma')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tür')
                    ->options([
                        'income' => 'Gelir',
                        'expense' => 'Gider',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Düzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Seçili Olanları Sil'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/yeni'),
            'edit' => Pages\EditCategory::route('/{record}/duzenle'),
        ];
    }
}

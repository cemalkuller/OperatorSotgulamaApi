<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentChannelResource\Pages;
use App\Filament\Resources\PaymentChannelResource\RelationManagers;
use App\Models\PaymentChannel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentChannelResource extends Resource
{
    protected static ?string $model = PaymentChannel::class;
    protected static ?string $navigationGroup = 'Finans';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Ödeme Kanalları';
    protected static ?int $navigationSort = 2;




    protected static ?string $pluralLabel = 'Ödeme Kanalları';
    protected static ?string $breadcrumb = 'Ödeme Kanalları';

    protected static ?string $title = 'Ödeme Kanalları';

    protected static ?string $modelLabel = 'Ödeme Kanalları';



    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Kanal Adı')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Kanal Adı')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Oluşturulma')->dateTime('d.m.Y H:i')->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentChannels::route('/'),
            'create' => Pages\CreatePaymentChannel::route('/yeni'),
            'edit' => Pages\EditPaymentChannel::route('/{record}/duzenle'),
        ];
    }

    // Sadece admin görsün
    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }
}

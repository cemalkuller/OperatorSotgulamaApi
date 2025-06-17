<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;
    protected static ?string $title = 'Nakit Akışı Ekle'; // veya 'Yeni Nakit Akışı'
    protected static ?string $breadcrumb = 'Nakit Akışı';
    protected static ?string $pluralLabel = 'Nakit Akışları';
    protected static ?string $navigationLabel = 'Nakit Akışı';
    protected static ?string $navigationGroup = 'Finans';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $modelLabel = 'Nakit Akışı';
    protected static ?string $modelPluralLabel = 'Nakit Akışları';
    protected static ?string $titleColumn = 'type';
    protected static ?string $slug = 'transactions';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

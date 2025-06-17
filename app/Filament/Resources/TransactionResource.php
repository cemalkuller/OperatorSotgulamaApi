<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\DateFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput\Mask;
use Illuminate\Database\Eloquent\Model;
class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Finans';
    protected static ?string $navigationLabel = 'Nakit Akışı';

    protected static ?string $pluralLabel = 'Nakit Akışı';
    protected static ?string $breadcrumb = 'Nakit Akışı';

    protected static ?string $title = 'Nakit Akışı';

    protected static ?string $modelLabel = 'Nakit Akışı'; // Tekil başlık

    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->label('Tür')
                    ->options([
                        'income' => 'Gelir',
                        'expense' => 'Gider',
                    ])
                    ->required(),
                TextInput::make('amount')
                    ->label('Tutar')
                    ->mask(RawJs::make('$money($input, \'.\', \',\', 2)'))
                    ->stripCharacters([',', '.'])
                    ->numeric()
                    ->required(),
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable(false),
                DatePicker::make('date')
                    ->label('Tarih')
                    ->default(now()->toDateString())
                    ->required(),
                Textarea::make('description')
                    ->label('Açıklama'),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Tür')
                    ->formatStateUsing(fn($state) => $state === 'income' ? 'Gelir' : 'Gider'),
                TextColumn::make('amount')
                    ->label('Tutar')
                    ->money('TRY', true),
                TextColumn::make('category.name')
                    ->label('Kategori'),
                TextColumn::make('date')
                    ->label('Tarih')
                    ->date('d.m.Y'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tür')
                    ->options([
                        'income' => 'Gelir',
                        'expense' => 'Gider',
                    ]),
                // Tarih aralığı filtresi
                Filter::make('date')
                    ->form([
                        DatePicker::make('from')
                            ->label('Başlangıç Tarihi'),
                        DatePicker::make('until')
                            ->label('Bitiş Tarihi'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('date', '<=', $date));
                    })
                    ->label('Tarih Aralığı'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/yeni'),
            'edit' => Pages\EditTransaction::route('/{record}/duzenle'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

}

<?php

namespace App\Filament\Pages;

use App\Exports\ArrayExport;
use App\Models\Transaction;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class CashflowReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.pages.cashflow-report';
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Finans';
    protected static ?string $navigationLabel = 'Nakit Akışı Raporu';
    protected static ?string $breadcrumb = 'Nakit Akışı Raporu';
    protected static ?string $title = 'Nakit Akışı Raporu';
    protected static ?string $slug = 'cashflow-report';
    protected static ?string $pluralLabel = 'Nakit Akışı Raporları';
    protected static ?string $modelLabel = 'Nakit Akışı Raporu';
    protected static ?string $modelPluralLabel = 'Nakit Akışı Raporları';

    public static ?string $description = 'Seçili tarih ve filtrelere göre tüm gelir/gider kayıtlarınızı buradan tablo halinde inceleyin veya Excel’e aktarın.';

    // Chart ve özet alanları için:
    public array $chartData = [];
    public ?float $income = null;
    public ?float $expense = null;
    public ?float $balance = null;

    // Varsayılan olarak bu ay filtreli başlasın

    public function mount()
    {
        // Filament Table filtrelerini mount sırasında tanımlamak için, varsayılan filtreyi tableFilters ile belirttik.
        $this->updateSummaryAndChart();
    }

    /**
     * Filament Table
     */
    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                TextColumn::make('date')->label('Tarih')->date('d.m.Y')->sortable(),
                TextColumn::make('type')->label('Tür')
                    ->formatStateUsing(fn($state) => $state === 'income' ? 'Gelir' : 'Gider')
                    ->badge()
                    ->color(fn($state) => $state === 'income' ? 'success' : 'danger'),
                TextColumn::make('category.name')->label('Kategori'),
                TextColumn::make('amount')->label('Tutar')->money('TRY', true)->sortable(),
                TextColumn::make('description')->label('Açıklama')->limit(30),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tür')
                    ->options([
                        'income' => 'Gelir',
                        'expense' => 'Gider',
                    ]),
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Kategori'),

                Filter::make('date')
                    ->form([
                        Select::make('preset')
                            ->label('Hazır Seçim')
                            ->options([
                                'this_month' => 'Bu Ay',
                                'last_month' => 'Geçen Ay',
                                'last_3_months' => 'Son 3 Ay',
                                'custom' => 'Özel',
                            ])
                            ->default('this_month'), // <<--- Varsayılanı burada ayarladık!
                        DatePicker::make('from')->label('Başlangıç Tarihi'),
                        DatePicker::make('until')->label('Bitiş Tarihi'),

                    ])
                    ->indicateUsing(function ($data) {
                        if ($data['preset'] === 'this_month')
                            return 'Bu Ay';
                        if ($data['preset'] === 'last_month')
                            return 'Geçen Ay';
                        if ($data['preset'] === 'last_3_months')
                            return 'Son 3 Ay';
                        if ($data['preset'] === 'custom' && $data['from'] && $data['until']) {
                            return $data['from'] . ' - ' . $data['until'];
                        }
                        return null;
                    })
                    ->query(function ($query, $data) {
                        // Preset’a göre query
                        if ($data['preset'] === 'this_month') {
                            $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                        } elseif ($data['preset'] === 'last_month') {
                            $query->whereBetween('date', [
                                now()->subMonthNoOverflow()->startOfMonth(),
                                now()->subMonthNoOverflow()->endOfMonth()
                            ]);
                        } elseif ($data['preset'] === 'last_3_months') {
                            $query->whereBetween('date', [
                                now()->subMonths(2)->startOfMonth(),
                                now()->endOfMonth()
                            ]);
                        } elseif ($data['preset'] === 'custom') {
                            if ($data['from'])
                                $query->whereDate('date', '>=', $data['from']);
                            if ($data['until'])
                                $query->whereDate('date', '<=', $data['until']);
                        } else {
                            // Hiçbiri seçili değilse default bu ay
                            $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                        }
                    }),
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Excel’e Aktar')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action('exportExcel'),
            ])
            ->paginationPageOptions([10, 25, 50])
            ->defaultSort('date', 'desc')
            ->searchable();
    }

    /**
     * Tablodaki filtrelere göre dinamik query oluşturur.
     */
    public function getFilteredQuery(): Builder
    {
        $query = Transaction::query()->with('category');

        // Tip ve kategori filtreleri (Filament kendi halleder, yine de burada örnek bıraktım)
        // if (request()->filled('tableFilters.type')) { ... }

        // Tarih aralığı/preset filtreleri (tablonun aktif filtreleri)
        $filter = $this->tableFilters['date'] ?? [];
        $preset = $filter['preset'] ?? 'this_month';
        $from = $filter['from'] ?? null;
        $until = $filter['until'] ?? null;

        if ($preset === 'this_month') {
            $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($preset === 'last_month') {
            $query->whereBetween('date', [
                now()->subMonthNoOverflow()->startOfMonth(),
                now()->subMonthNoOverflow()->endOfMonth()
            ]);
        } elseif ($preset === 'last_3_months') {
            $query->whereBetween('date', [
                now()->subMonths(2)->startOfMonth(),
                now()->endOfMonth()
            ]);
        } elseif ($preset === 'custom') {
            if ($from)
                $query->whereDate('date', '>=', $from);
            if ($until)
                $query->whereDate('date', '<=', $until);
        } else {
            // default: bu ay
            $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        return $query;
    }

    /**
     * Chart ve özet verileri güncellenir
     */
    public function updateSummaryAndChart()
    {
        $query = $this->getFilteredQuery()->clone();

        // Grafik: Aylık toplamlara böl
        $incomeData = (clone $query)->where('type', 'income')
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $expenseData = (clone $query)->where('type', 'expense')
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $months = array_unique(array_merge(array_keys($incomeData), array_keys($expenseData)));
        sort($months);

        $incomeSeries = [];
        $expenseSeries = [];

        foreach ($months as $month) {
            $incomeSeries[] = $incomeData[$month] ?? 0;
            $expenseSeries[] = $expenseData[$month] ?? 0;
        }

        $this->chartData = [
            'labels' => $months,
            'income' => $incomeSeries,
            'expense' => $expenseSeries,
        ];

        // Genel toplam
        $this->income = (clone $query)->where('type', 'income')->sum('amount');
        $this->expense = (clone $query)->where('type', 'expense')->sum('amount');
        $this->balance = $this->income - $this->expense;
    }

    /**
     * Table filtreleri değiştiğinde chartı ve toplamları güncelle
     */
    public function updated($property)
    {
        // Eğer tableFilters değiştiyse chartı ve özetleri güncelle
        if (str_starts_with($property, 'tableFilters')) {
            $this->updateSummaryAndChart();
        }
    }

    /**
     * Excel export
     */
    public function exportExcel()
    {
        $rows = $this->getFilteredQuery()->get()->map(function ($item) {
            return [
                'Tarih' => $item->date,
                'Tür' => $item->type === 'income' ? 'Gelir' : 'Gider',
                'Kategori' => $item->category?->name,
                'Tutar' => $item->amount,
                'Açıklama' => $item->description,
            ];
        })->toArray();

        return Excel::download(
            new ArrayExport($rows),
            'nakit_akisi_raporu.xlsx'
        );
    }
}

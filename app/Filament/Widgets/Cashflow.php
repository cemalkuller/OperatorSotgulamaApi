<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\Widget;
use Illuminate\View\View;

class Cashflow extends Widget
{
    protected static string $view = 'filament.widgets.cashflow';

    public function render(): View
    {
        $income = Transaction::where('type', 'income')->sum('amount');
        $expense = Transaction::where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        return view('filament.widgets.cashflow', compact('income', 'expense', 'balance'));
    }
}

<div class="grid grid-cols-3 gap-4">
    <div>
        <div class="font-bold">Gelir</div>
        <div>{{ number_format($income, 2) }} ₺</div>
    </div>
    <div>
        <div class="font-bold">Gider</div>
        <div>{{ number_format($expense, 2) }} ₺</div>
    </div>
    <div>
        <div class="font-bold">Bakiye</div>
        <div>{{ number_format($balance, 2) }} ₺</div>
    </div>
</div>
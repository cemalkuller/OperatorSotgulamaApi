<x-filament::page>
    {{-- Chart --}}
    <div class="mb-6">
        <canvas id="cashflowChart" height="80"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('cashflowChart').getContext('2d');
            if (window.cashflowChartObj) window.cashflowChartObj.destroy();
            window.cashflowChartObj = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Gelir',
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            data: @json($chartData['income']),
                        },
                        {
                            label: 'Gider',
                            backgroundColor: 'rgba(239, 68, 68, 0.7)',
                            data: @json($chartData['expense']),
                        },
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Aylık Nakit Akışı' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
        // Dinamik güncelleme için Filament reaktif event dinleme (isteğe bağlı)
        document.addEventListener('livewire:load', function () {
            Livewire.hook('component.updated', (component, el) => {
                if (component.name.includes('cashflow-report')) {
                    setTimeout(() => {
                        document.dispatchEvent(new Event('DOMContentLoaded'));
                    }, 200);
                }
            });
        });
    </script>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Gelir --}}
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="flex-shrink-0 bg-green-100 p-2 rounded-full">
                <x-heroicon-o-arrow-trending-up class="w-6 h-6 text-green-600" />
            </div>
            <div>
                <div class="text-xs font-medium text-gray-500 uppercase">Gelir</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($income, 2) }} ₺</div>
            </div>
        </div>

        {{-- Gider --}}
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="flex-shrink-0 bg-red-100 p-2 rounded-full">
                <x-heroicon-o-arrow-trending-down class="w-6 h-6 text-red-600" />
            </div>
            <div>
                <div class="text-xs font-medium text-gray-500 uppercase">Gider</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($expense, 2) }} ₺</div>
            </div>
        </div>

        {{-- Bakiye --}}
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4" style="padding: 20px 10px">
            <div class="flex-shrink-0 {{ $balance >= 0 ? 'bg-blue-100' : 'bg-red-100' }} p-2 rounded-full">
                <x-heroicon-o-currency-dollar class="w-6 h-6 {{ $balance >= 0 ? 'text-blue-600' : 'text-red-600' }}" />
            </div>
            <div>
                <div class="text-xs font-medium text-gray-500 uppercase">Bakiye</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($balance, 2) }} ₺</div>
            </div>
        </div>
    </div>

    {{ $this->table }}
</x-filament::page>
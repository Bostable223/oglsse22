@extends('layouts.app')

@section('title', 'Admin - Analitika paketa')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Admin Panel', 'url' => route('admin.dashboard')],
        ['title' => 'Paketi', 'url' => route('admin.packages')],
        ['title' => 'Analitika', 'url' => route('admin.packages.analytics')]
    ]" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Analitika paketa</h1>
                <p class="text-gray-600 mt-2">Pregled popularnosti i prihoda od paketa</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.packages') }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold">
                    <i class="fas fa-box mr-2"></i> Paketi
                </a>
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('admin.packages.analytics') }}" method="GET" class="space-y-4">
                <div class="flex flex-wrap items-end gap-4">
                    <!-- Start Date -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-blue-600 mr-1"></i>
                            Datum početka
                        </label>
                        <input type="date" 
                               name="start_date" 
                               value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- End Date -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-blue-600 mr-1"></i>
                            Datum kraja
                        </label>
                        <input type="date" 
                               name="end_date" 
                               value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold whitespace-nowrap">
                            <i class="fas fa-filter mr-2"></i> Primeni
                        </button>
                        <a href="{{ route('admin.packages.analytics') }}" 
                           class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold whitespace-nowrap">
                            <i class="fas fa-redo mr-2"></i> Resetuj
                        </a>
                    </div>
                </div>

                <!-- Quick Date Presets -->
                <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
                    <span class="text-sm text-gray-600 font-medium mr-2">Brzi izbor:</span>
                    <button type="button" 
                            onclick="setDateRange('today')"
                            class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 font-semibold">
                        Danas
                    </button>
                    <button type="button" 
                            onclick="setDateRange('yesterday')"
                            class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 font-semibold">
                        Juče
                    </button>
                    <button type="button" 
                            onclick="setDateRange('last7days')"
                            class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 font-semibold">
                        Poslednjih 7 dana
                    </button>
                    <button type="button" 
                            onclick="setDateRange('last30days')"
                            class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 font-semibold">
                        Poslednjih 30 dana
                    </button>
                    <button type="button" 
                            onclick="setDateRange('thisMonth')"
                            class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 font-semibold">
                        Ovaj mesec
                    </button>
                    <button type="button" 
                            onclick="setDateRange('lastMonth')"
                            class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 font-semibold">
                        Prošli mesec
                    </button>
                    <button type="button" 
                            onclick="setDateRange('thisYear')"
                            class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 font-semibold">
                        Ova godina
                    </button>
                    <button type="button" 
                            onclick="setDateRange('allTime')"
                            class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 font-semibold">
                        Sve vreme
                    </button>
                </div>

                <!-- Current Range Display -->
                <div class="text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Prikazani podaci za period: 
                    <strong>{{ $startDate->format('d.m.Y') }}</strong> do 
                    <strong>{{ $endDate->format('d.m.Y') }}</strong>
                    ({{ $startDate->diffInDays($endDate) + 1 }} dana)
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Ukupan prihod</p>
                    <p class="text-3xl font-bold">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                    <p class="text-blue-100 text-sm mt-1">RSD</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-dollar-sign text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">Prodatih paketa</p>
                    <p class="text-3xl font-bold">{{ $totalSales ?? 0 }}</p>
                    <p class="text-green-100 text-sm mt-1">Ukupno</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm mb-1">Top aktivni</p>
                    <p class="text-3xl font-bold">{{ $activePromotions['top'] ?? 0 }}</p>
                    <p class="text-indigo-100 text-sm mt-1">Oglasa</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-arrow-up text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-orange-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm mb-1">Featured aktivni</p>
                    <p class="text-3xl font-bold">{{ $activePromotions['featured'] ?? 0 }}</p>
                    <p class="text-yellow-100 text-sm mt-1">Oglasa</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-star text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Package Usage Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                Distribucija korišćenja paketa
            </h3>
            @if($packages->sum('listings_count') > 0)
                <div class="relative h-[300px]">
                    <canvas id="packageUsageChart"></canvas>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-chart-pie text-4xl mb-4"></i>
                    <p>Nema podataka za prikaz</p>
                </div>
            @endif
        </div>

        <!-- Package Type Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                Korišćenje po tipu
            </h3>
            @if(array_sum($usageByType ?? []) > 0)
                <div class="relative h-[300px]">
                    <canvas id="packageTypeChart"></canvas>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-chart-bar text-4xl mb-4"></i>
                    <p>Nema podataka za prikaz</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Revenue by Package -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                Prihod po paketu
            </h3>
            @if($revenueByPackage->sum('total_revenue') > 0)
                <div class="relative h-[300px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-money-bill-wave text-4xl mb-4"></i>
                    <p>Nema podataka za prikaz</p>
                </div>
            @endif
        </div>

        <!-- Monthly Trend -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line text-purple-600 mr-2"></i>
                Mesečni trend
            </h3>
            @if(collect($monthlyTrend)->sum('count') > 0)
                <div class="relative h-[300px]">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-chart-line text-4xl mb-4"></i>
                    <p>Nema podataka za prikaz</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Popular Packages & Recent Purchases -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Most Popular Packages -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-fire text-red-600 mr-2"></i>
                Najpopularniji paketi
            </h3>
            @if($popularPackages->count() > 0 && $popularPackages->sum('listings_count') > 0)
                <div class="space-y-3">
                    @foreach($popularPackages->where('listings_count', '>', 0) as $package)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $package->type === 'featured' ? 'bg-yellow-100' : 'bg-blue-100' }}">
                                    <i class="fas fa-{{ $package->type === 'featured' ? 'star text-yellow-600' : 'arrow-up text-blue-600' }}"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $package->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $package->duration_days }} dana</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-900">{{ $package->listings_count }}</div>
                                <div class="text-xs text-gray-500">korišćenja</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-fire text-4xl mb-4"></i>
                    <p>Još nema prodatih paketa</p>
                </div>
            @endif
        </div>

        <!-- Recent Purchases -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-clock text-blue-600 mr-2"></i>
                Nedavne kupovine
            </h3>
            @if($recentPurchases->count() > 0)
                <div class="space-y-3">
                    @foreach($recentPurchases as $listing)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <img src="{{ $listing->user->avatarUrl() }}" 
                                     alt="{{ $listing->user->name }}" 
                                     class="w-10 h-10 rounded-full">
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">
                                        {{ Str::limit($listing->title, 30) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $listing->user->name }} • 
                                        {{ $listing->promoted_at ? $listing->promoted_at->diffForHumans() : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-semibold px-2 py-1 rounded
                                    {{ $listing->package->type === 'featured' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ Str::limit($listing->package->name, 15) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-clock text-4xl mb-4"></i>
                    <p>Nema nedavnih kupovina</p>
                </div>
            @endif
        </div>
    </div>

    <!-- All Packages Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-table text-gray-600 mr-2"></i>
                Svi paketi - Detaljna statistika
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tip</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trajanje</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cena</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Korišćenja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prihod</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($packages as $package)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $package->name }}</div>
                                @if($package->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($package->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $package->type === 'featured' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $package->type === 'featured' ? 'Featured' : 'Top' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $package->duration_days }} dana
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ number_format($package->price, 0, ',', '.') }} {{ $package->currency }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="text-lg font-bold text-gray-900">{{ $package->listings_count }}</div>
                                    <div class="ml-2 text-xs text-gray-500">puta</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-lg font-bold text-green-600">
                                    {{ number_format($package->price * $package->listings_count, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $package->currency }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($package->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktivan
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Neaktivan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Nema paketa za prikaz
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Only initialize charts if Chart.js is loaded
if (typeof Chart !== 'undefined') {
    
    // Package Usage Distribution (Pie Chart)
    @if($packages->sum('listings_count') > 0)
    const packageUsageCtx = document.getElementById('packageUsageChart');
    if (packageUsageCtx) {
        new Chart(packageUsageCtx.getContext('2d'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($packages->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($packages->pluck('listings_count')) !!},
                    backgroundColor: [
                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                        '#EC4899', '#14B8A6', '#F97316', '#06B6D4', '#6366F1'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' korišćenja';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Package Type Distribution (Doughnut Chart)
    @if(array_sum($usageByType ?? []) > 0)
    const packageTypeCtx = document.getElementById('packageTypeChart');
    if (packageTypeCtx) {
        new Chart(packageTypeCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Top Paketi', 'Featured Paketi', 'Besplatni oglasi'],
                datasets: [{
                    data: [
                        {{ $usageByType['top'] ?? 0 }},
                        {{ $usageByType['featured'] ?? 0 }},
                        {{ $usageByType['free'] ?? 0 }}
                    ],
                    backgroundColor: ['#3B82F6', '#F59E0B', '#9CA3AF'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
    @endif

    // Revenue by Package (Bar Chart)
    @if($revenueByPackage->sum('total_revenue') > 0)
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($revenueByPackage->pluck('package_name')) !!},
                datasets: [{
                    label: 'Prihod (RSD)',
                    data: {!! json_encode($revenueByPackage->pluck('total_revenue')) !!},
                    backgroundColor: '#10B981',
                    borderColor: '#059669',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' RSD';
                            }
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString() + ' RSD';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Monthly Trend (Line Chart)
    @if(collect($monthlyTrend)->sum('count') > 0)
    const monthlyTrendCtx = document.getElementById('monthlyTrendChart');
    if (monthlyTrendCtx) {
        new Chart(monthlyTrendCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode(collect($monthlyTrend)->pluck('month')) !!},
                datasets: [
                    {
                        label: 'Broj prodaja',
                        data: {!! json_encode(collect($monthlyTrend)->pluck('count')) !!},
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Prihod (RSD)',
                        data: {!! json_encode(collect($monthlyTrend)->pluck('revenue')) !!},
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Broj prodaja'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'Prihod (RSD)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

} else {
    console.error('Chart.js is not loaded');
}

// Date Range Quick Presets
function setDateRange(preset) {
    const today = new Date();
    let startDate, endDate;
    
    switch(preset) {
        case 'today':
            startDate = endDate = formatDate(today);
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = formatDate(yesterday);
            break;
        case 'last7days':
            endDate = formatDate(today);
            const last7 = new Date(today);
            last7.setDate(last7.getDate() - 6);
            startDate = formatDate(last7);
            break;
        case 'last30days':
            endDate = formatDate(today);
            const last30 = new Date(today);
            last30.setDate(last30.getDate() - 29);
            startDate = formatDate(last30);
            break;
        case 'thisMonth':
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            startDate = formatDate(firstDay);
            endDate = formatDate(today);
            break;
        case 'lastMonth':
            const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
            const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            startDate = formatDate(lastMonthStart);
            endDate = formatDate(lastMonthEnd);
            break;
        case 'thisYear':
            const yearStart = new Date(today.getFullYear(), 0, 1);
            startDate = formatDate(yearStart);
            endDate = formatDate(today);
            break;
        case 'allTime':
            // Set to very old date for "all time"
            startDate = '2020-01-01';
            endDate = formatDate(today);
            break;
    }
    
    document.querySelector('input[name="start_date"]').value = startDate;
    document.querySelector('input[name="end_date"]').value = endDate;
    
    // Auto-submit form
    document.querySelector('form').submit();
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
</script>
@endpush

@extends('layouts.app')

@section('title', 'Admin - Analitika paketa')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Ukupan prihod</p>
                    <p class="text-3xl font-bold">{{ number_format($totalRevenue, 0, ',', '.') }}</p>
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
                    <p class="text-3xl font-bold">{{ $totalSales }}</p>
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
                    <p class="text-3xl font-bold">{{ $activePromotions['top'] }}</p>
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
                    <p class="text-3xl font-bold">{{ $activePromotions['featured'] }}</p>
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
            <canvas id="packageUsageChart" height="300"></canvas>
        </div>

        <!-- Package Type Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                Korišćenje po tipu
            </h3>
            <canvas id="packageTypeChart" height="300"></canvas>
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
            <canvas id="revenueChart" height="300"></canvas>
        </div>

        <!-- Monthly Trend -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line text-purple-600 mr-2"></i>
                Mesečni trend (poslednih 6 meseci)
            </h3>
            <canvas id="monthlyTrendChart" height="300"></canvas>
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
            <div class="space-y-3">
                @foreach($popularPackages as $package)
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
        </div>

        <!-- Recent Purchases -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-clock text-blue-600 mr-2"></i>
                Nedavne kupovine
            </h3>
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
                                {{ $listing->package->name }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
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
                    @foreach($packages as $package)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $package->name }}</div>
                                <div class="text-sm text-gray-500">{{ $package->description }}</div>
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Package Usage Distribution (Pie Chart)
const packageUsageCtx = document.getElementById('packageUsageChart').getContext('2d');
new Chart(packageUsageCtx, {
    type: 'pie',
    data: {
        labels: @json($packages->pluck('name')),
        datasets: [{
            data: @json($packages->pluck('listings_count')),
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
            legend: {
                position: 'right',
            },
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

// Package Type Distribution (Doughnut Chart)
const packageTypeCtx = document.getElementById('packageTypeChart').getContext('2d');
new Chart(packageTypeCtx, {
    type: 'doughnut',
    data: {
        labels: ['Top Paketi', 'Featured Paketi', 'Besplatni oglasi'],
        datasets: [{
            data: [
                {{ $usageByType['top'] }},
                {{ $usageByType['featured'] }},
                {{ $usageByType['free'] }}
            ],
            backgroundColor: ['#3B82F6', '#F59E0B', '#9CA3AF'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Revenue by Package (Bar Chart)
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: @json($revenueByPackage->pluck('package_name')),
        datasets: [{
            label: 'Prihod (RSD)',
            data: @json($revenueByPackage->pluck('total_revenue')),
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
            legend: {
                display: false
            },
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

// Monthly Trend (Line Chart)
const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
new Chart(monthlyTrendCtx, {
    type: 'line',
    data: {
        labels: @json(collect($monthlyTrend)->pluck('month')),
        datasets: [
            {
                label: 'Broj prodaja',
                data: @json(collect($monthlyTrend)->pluck('count')),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y'
            },
            {
                label: 'Prihod (RSD)',
                data: @json(collect($monthlyTrend)->pluck('revenue')),
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
</script>
@endpush
@endsection
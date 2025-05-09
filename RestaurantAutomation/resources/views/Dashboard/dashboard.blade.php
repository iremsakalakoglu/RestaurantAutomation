<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central Perk - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                Central<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>Perk
                <span class="text-gray-600 text-lg">Admin</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-gray-600">Hoş geldiniz, {{ Auth::user()->name }}</span>
                <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-[#d4a373] hover:text-[#c48c63] transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Çıkış Yap
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="fixed left-0 top-16 h-full w-64 bg-white shadow-md">
        <div class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-700 bg-gray-100 rounded">
                        <i class="fas fa-chart-line w-6"></i>
                        <span>Genel Bakış</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-utensils w-6"></i>
                        <span>Ürünler</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categories') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-list w-6"></i>
                        <span>Kategoriler</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.orders') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-shopping-cart w-6"></i>
                        <span>Siparişler</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tables') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.tables') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-chair w-6"></i>
                        <span>Masalar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-users w-6"></i>
                        <span>Kullanıcılar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.inventory') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-box w-6"></i>
                        <span>Stok Yönetimi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-cog w-6"></i>
                        <span>Ayarlar</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 mt-16 p-8">
        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- İstatistik Kartları -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
            <div>
                        <p class="text-gray-500">Günlük Satış</p>
                        <h3 class="text-2xl font-bold">₺{{ number_format($dailySales ?? 0, 2) }}</h3>
                    </div>
                    <div class="text-green-500">
                        <i class="fas fa-chart-line text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Toplam Sipariş</p>
                        <h3 class="text-2xl font-bold">{{ $totalOrders ?? 0 }}</h3>
                    </div>
                    <div class="text-blue-500">
                        <i class="fas fa-shopping-cart text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Kayıtlı Toplam Müşteri</p>
                        <h3 class="text-2xl font-bold">{{ $totalCustomers ?? 0 }}</h3>
                    </div>
                    <div class="text-purple-500">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">Kritik Stok</p>
                        <h3 class="text-2xl font-bold">{{ $stockAlerts ?? 0 }}</h3>
                    </div>
                    <div class="text-red-500">
                        <i class="fas fa-exclamation-triangle text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alt Grafik Grubu -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Müşteri Artış Grafiği -->
            <div class="bg-white rounded-lg shadow-md p-6" style="min-height: 350px;">
                <h2 class="text-xl font-semibold mb-4">Aylık Müşteri Artışı</h2>
                <div id="customerGrowthChart" class="w-full" style="height: 300px;"></div>
            </div>

            <!-- En Çok Satan Ürünler Grafiği -->
            <div class="bg-white rounded-lg shadow-md p-6" style="min-height: 350px;">
                <h2 class="text-xl font-semibold mb-4">En Çok Satan Ürünler</h2>
                <div id="topProductsChart" class="w-full" style="height: 300px;"></div>
            </div>
        </div>

        <!-- Günlük Ciro ve Sipariş Grafiği -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Son 7 Günün Ciro ve Sipariş Analizi</h2>
            <div id="revenueOrdersChart" class="w-full" style="height: 400px;"></div>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Başarılı!',
                text: '{{ session('success') }}',
                icon: 'success',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        </script>
    @endif

    <script>
        // Grafik verilerini PHP'den al
        const chartData = @json($lastSevenDaysData);
        
        // Verileri grafik için hazırla
        const dates = chartData.map(item => item.date);
        const revenues = chartData.map(item => item.revenue);
        const orders = chartData.map(item => item.orders);

        // Grafik options
        const options = {
            series: [{
                name: 'Ciro (₺)',
                type: 'area',
                data: revenues
            }, {
                name: 'Sipariş Sayısı',
                type: 'line',
                data: orders
            }],
            chart: {
                height: 400,
                type: 'line',
                toolbar: {
                    show: false
                }
            },
            stroke: {
                curve: 'smooth',
                width: [2, 2]
            },
            fill: {
                type: ['gradient', 'solid'],
                opacity: [0.1, 1],
            },
            colors: ['#d4a373', '#4f46e5'],
            title: {
                text: undefined
            },
            xaxis: {
                categories: dates,
                labels: {
                    style: {
                        colors: '#64748b'
                    }
                }
            },
            yaxis: [{
                title: {
                    text: 'Ciro (₺)',
                    style: {
                        color: '#d4a373'
                    }
                },
                labels: {
                    style: {
                        colors: '#64748b'
                    },
                    formatter: function(val) {
                        return '₺' + val.toFixed(2);
                    }
                }
            }, {
                opposite: true,
                title: {
                    text: 'Sipariş Sayısı',
                    style: {
                        color: '#4f46e5'
                    }
                },
                labels: {
                    style: {
                        colors: '#64748b'
                    }
                }
            }],
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                labels: {
                    colors: '#64748b'
                }
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4
            },
            tooltip: {
                y: [{
                    formatter: function(value) {
                        return '₺' + value.toFixed(2);
                    }
                }, {
                    formatter: function(value) {
                        return value + ' sipariş';
                    }
                }]
            }
        };

        // Grafiği oluştur
        const chart = new ApexCharts(document.querySelector("#revenueOrdersChart"), options);
        chart.render();

        // Müşteri Artış Grafiği
        const customerData = @json($customerGrowthData);
        const customerGrowthOptions = {
            series: [{
                name: 'Yeni Müşteriler',
                type: 'column',
                data: customerData.map(item => item.customers)
            }, {
                name: 'Aylık Değişim (%)',
                type: 'line',
                data: customerData.map(item => item.growth_rate)
            }],
            chart: {
                height: 300,
                type: 'line',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    speed: 500
                }
            },
            stroke: {
                width: [0, 2]
            },
            colors: ['#d4a373', '#10b981'],
            title: {
                text: undefined
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1],
                formatter: function(val) {
                    if (val === 0) return '0%';
                    return val > 0 ? '+' + val.toFixed(1) + '%' : val.toFixed(1) + '%';
                },
                style: {
                    fontSize: '14px',
                    colors: ['#10b981', '#ef4444'],
                    fontWeight: 600
                },
                offsetY: -8
            },
            xaxis: {
                categories: customerData.map(item => item.month),
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '13px'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: [{
                title: {
                    text: 'Yeni Müşteri Sayısı',
                    style: {
                        color: '#d4a373'
                    }
                },
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '13px'
                    }
                }
            }, {
                opposite: true,
                title: {
                    text: 'Aylık Değişim (%)',
                    style: {
                        color: '#10b981'
                    }
                },
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '13px'
                    },
                    formatter: function(val) {
                        if (val === 0) return '0%';
                        return val > 0 ? '+' + val.toFixed(1) + '%' : val.toFixed(1) + '%';
                    }
                }
            }],
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                labels: {
                    colors: '#64748b'
                }
            },
            fill: {
                opacity: [0.85, 1]
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            },
            tooltip: {
                y: [{
                    formatter: function(value) {
                        return value + ' yeni müşteri';
                    }
                }, {
                    formatter: function(value, { seriesIndex, dataPointIndex, w }) {
                        const data = customerData[dataPointIndex];
                        let message = value === 0 ? 'Değişim yok' : 
                            value > 0 ? `+${value.toFixed(1)}% artış` : `${value.toFixed(1)}% azalış`;
                        
                        return `${message} (Önceki ay: ${data.previous_month} müşteri)`;
                    }
                }]
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: '300px'
                    },
                    dataLabels: {
                        offsetY: 0
                    }
                }
            }]
        };

        // Müşteri artış grafiğini oluştur
        const customerGrowthChart = new ApexCharts(document.querySelector("#customerGrowthChart"), customerGrowthOptions);
        customerGrowthChart.render();

        // En Çok Satan Ürünler Grafiği
        const topProductsData = @json($topSellingProducts);
        const topProductsOptions = {
            series: [{
                name: 'Satış Adedi',
                data: topProductsData.map(item => item.quantity)
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    speed: 500
                }
            },
            colors: ['#d4a373'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                    barHeight: '50%',
                    distributed: true,
                    dataLabels: {
                        position: 'center'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val + ' adet';
                },
                style: {
                    fontSize: '14px',
                    colors: ['#ffffff'],
                    fontWeight: 600
                },
                offsetX: 30
            },
            xaxis: {
                categories: topProductsData.map(item => item.name),
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '13px'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '13px'
                    },
                    offsetX: -10
                }
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            },
            tooltip: {
                enabled: true,
                theme: 'light',
                style: {
                    fontSize: '12px'
                },
                y: {
                    formatter: function (val) {
                        return val + ' adet satıldı';
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: '300px'
                    },
                    plotOptions: {
                        bar: {
                            barHeight: '60%'
                        }
                    },
                    dataLabels: {
                        offsetX: 0
                    }
                }
            }]
        };

        // En çok satan ürünler grafiğini oluştur
        const topProductsChart = new ApexCharts(document.querySelector("#topProductsChart"), topProductsOptions);
        topProductsChart.render();
    </script>
</body>
</html>

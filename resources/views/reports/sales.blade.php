<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Date Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date" id="from-date" 
                                   value="{{ now()->subDays(30)->format('Y-m-d') }}"
                                   class="border-gray-300 rounded-md shadow-sm w-full focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date" id="to-date" 
                                   value="{{ now()->format('Y-m-d') }}"
                                   class="border-gray-300 rounded-md shadow-sm w-full focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <button id="load-report" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md shadow">
                                Load Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Total Orders</div>
                        <div class="text-3xl font-bold text-gray-900" id="total-orders">-</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Gross Revenue</div>
                        <div class="text-3xl font-bold text-gray-900" id="gross-revenue">-</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Total Discounts</div>
                        <div class="text-3xl font-bold text-green-600" id="total-discounts">-</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Net Revenue</div>
                        <div class="text-3xl font-bold text-indigo-600" id="net-revenue">-</div>
                    </div>
                </div>
            </div>

            <!-- Daily Revenue Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Daily Revenue Trend</h3>
                    <div class="overflow-x-auto">
                        <canvas id="daily-chart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Products Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Top 10 Products</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rank
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Product Name
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity Sold
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Revenue
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="top-products" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        let dailyChart = null;

        async function loadReport() {
            const fromDate = document.getElementById('from-date').value;
            const toDate = document.getElementById('to-date').value;

            try {
                const response = await fetch(`{{ route('reports.sales.data') }}?from=${fromDate}&to=${toDate}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                // Update summary cards
                document.getElementById('total-orders').textContent = data.summary.orders || 0;
                document.getElementById('gross-revenue').textContent = 'Rp ' + formatNumber(data.summary.gross || 0);
                document.getElementById('total-discounts').textContent = 'Rp ' + formatNumber(data.summary.discounts || 0);
                document.getElementById('net-revenue').textContent = 'Rp ' + formatNumber(data.summary.net || 0);

                // Update daily chart
                updateDailyChart(data.daily);

                // Update top products table
                updateTopProducts(data.topProducts);

            } catch (error) {
                console.error('Error loading report:', error);
                alert('Failed to load report data');
            }
        }

        function updateDailyChart(dailyData) {
            const ctx = document.getElementById('daily-chart').getContext('2d');
            
            const labels = dailyData.map(d => d.day);
            const values = dailyData.map(d => parseInt(d.total));

            if (dailyChart) {
                dailyChart.destroy();
            }

            dailyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Revenue (Rp)',
                        data: values,
                        borderColor: 'rgb(79, 70, 229)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: Rp ' + formatNumber(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + formatNumber(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateTopProducts(products) {
            const tbody = document.getElementById('top-products');
            
            if (products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No products found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = products.map((product, index) => `
                <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full ${index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'} font-bold">
                                ${index + 1}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">${product.product_name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm text-gray-900">${formatNumber(product.qty)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm font-semibold text-gray-900">Rp ${formatNumber(product.revenue)}</div>
                    </td>
                </tr>
            `).join('');
        }

        function formatNumber(num) {
            return parseInt(num).toLocaleString('id-ID');
        }

        // Load report on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadReport();
        });

        // Load report on button click
        document.getElementById('load-report').addEventListener('click', loadReport);

        // Quick date range buttons (optional enhancement)
        function setDateRange(days) {
            const toDate = new Date();
            const fromDate = new Date();
            fromDate.setDate(fromDate.getDate() - days);
            
            document.getElementById('from-date').value = fromDate.toISOString().split('T')[0];
            document.getElementById('to-date').value = toDate.toISOString().split('T')[0];
            
            loadReport();
        }
    </script>
    @endpush
</x-app-layout>

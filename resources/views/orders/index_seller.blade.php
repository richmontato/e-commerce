<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white">
            {{ __('Orders Containing Your Products') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-indigo-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8">
                    @if($orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-indigo-600 to-indigo-700">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Items Sold</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Subtotal</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Placed</th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($orders as $order)
                                        @php
                                            $sellerSubtotal = $order->items->sum(fn($item) => $item->qty * $item->unit_price);
                                            $itemSummary = $order->items->map(function ($item) {
                                                return $item->product->name ?? $item->product_name;
                                            })->filter()->implode(', ');
                                        @endphp
                                        <tr class="hover:bg-indigo-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">#{{ $order->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-semibold text-gray-800">{{ $order->user->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->user->email ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-normal text-sm text-gray-700">
                                                {{ $itemSummary ?: 'Items unavailable' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-800">
                                                Rp {{ number_format($sellerSubtotal, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusBadgeMap = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'processing' => 'bg-blue-100 text-blue-800',
                                                        'shipped' => 'bg-green-100 text-green-800',
                                                        'delivered' => 'bg-gray-100 text-gray-800',
                                                        'canceled' => 'bg-red-100 text-red-800',
                                                    ];
                                                    $statusBadgeClasses = $statusBadgeMap[$order->status] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="px-3 py-1.5 inline-flex text-xs leading-5 font-bold rounded-full {{ $statusBadgeClasses }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $order->created_at?->format('d M Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-semibold hover:bg-indigo-50 px-4 py-2 rounded-lg transition">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <svg class="w-32 h-32 text-gray-300 mx-auto mb-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-gray-500 text-xl font-semibold">No orders for your products yet</p>
                            <p class="text-gray-400 mt-2">Orders containing your catalog will be listed here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

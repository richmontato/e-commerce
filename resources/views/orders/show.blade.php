<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }} #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Customer Information</h3>
                            <p><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>

                            @if($order->shipping_address)
                                <div class="mt-4">
                                    <h4 class="text-md font-medium text-gray-900">Shipping Address</h4>
                                    <p class="whitespace-pre-line">{{ $order->shipping_address }}</p>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Order Information</h3>
                            <p><strong>Total:</strong> Rp. {{ number_format($order->total_amount, 2) }}</p>
                            <p><strong>Shipping Method:</strong> {{ $order->shipping_method_label }}</p>
                            <p><strong>Payment Method:</strong> {{ strtoupper(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}</p>
                            <p><strong>Status:</strong> <span class="font-semibold">{{ ucfirst($order->status) }}</span></p>

                            @if($canUpdateStatus && count($statusTransitions) > 0)
                                <form action="{{ route('orders.update', $order) }}" method="POST" class="mt-4">
                                    @csrf
                                    @method('PUT')
                                    <label for="status" class="block text-sm font-medium text-gray-700">Update Status</label>
                                    <div class="flex items-center space-x-2">
                                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                            <option value="{{ $order->status }}" selected disabled>{{ ucfirst($order->status) }}</option>
                                            @foreach($statusTransitions as $statusOption)
                                                <option value="{{ $statusOption }}">{{ ucfirst($statusOption) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="mt-1 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Update
                                        </button>
                                    </div>
                                    @error('status')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </form>
                            @elseif($canUpdateStatus)
                                <p class="mt-4 text-sm text-gray-600">This order has already been completed or canceled, so no further updates are required.</p>
                            @elseif($viewerRole === 'seller')
                                <p class="mt-4 text-sm text-gray-600">Anda dapat memperbarui status untuk pesanan yang berisi produk Anda ketika statusnya masih dapat diubah.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                        <table class="min-w-full divide-y divide-gray-200 mt-4">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($visibleItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name ?? $item->product_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->qty }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp. {{ number_format($item->unit_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-sm text-gray-500">No items available for your catalog in this order.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex items-center space-x-4">
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            &larr; Back to Orders
                        </a>
                        @if($viewerRole !== 'seller')
                            <a href="{{ route('orders.print', $order) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                Print Invoice
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
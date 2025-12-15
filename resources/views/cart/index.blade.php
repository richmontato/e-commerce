<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white">Shopping Cart</h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center shadow-lg">
                    <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($cartItems->count() > 0)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">Product</th>
                                <th class="px-6 py-4 text-right font-semibold">Price</th>
                                <th class="px-6 py-4 text-center font-semibold">Quantity</th>
                                <th class="px-6 py-4 text-right font-semibold">Subtotal</th>
                                <th class="px-6 py-4 font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                                <tr class="border-b border-gray-200 hover:bg-blue-50 transition">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center">
                                            @if($item->product->image_url)
                                                <img src="{{ $item->product->image_url }}" class="w-20 h-20 object-cover rounded-lg mr-4 shadow-md">
                                            @else
                                                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg mr-4 flex items-center justify-center">
                                                    <svg class="w-10 h-10 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('products.show', $item->product->slug) }}" class="font-bold text-gray-800 hover:text-blue-600 transition">
                                                    {{ $item->product->name }}
                                                </a>
                                                <p class="text-sm text-gray-500 mt-1">{{ $item->product->category }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right text-gray-700 font-semibold">Rp {{ number_format($item->product->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-5">
                                        <input type="number" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" 
                                               class="border-2 border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg w-20 text-center font-semibold cart-quantity"
                                               data-cart-id="{{ $item->id }}">
                                    </td>
                                    <td class="px-6 py-5 text-right font-bold text-blue-600 text-lg">
                                        <span class="cart-item-subtotal" data-price="{{ $item->product->price }}">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 hover:bg-red-50 px-4 py-2 rounded-lg transition font-semibold">
                                                <svg class="w-5 h-5 inline" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-8 bg-gradient-to-r from-gray-50 to-blue-50 border-t-4 border-blue-600">
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-2xl font-bold text-gray-800">Total:</span>
                            <span class="text-3xl font-bold text-blue-600" id="cart-total">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 text-red-600 hover:text-red-800 hover:bg-red-50 px-5 py-3 rounded-lg font-semibold transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Clear Cart
                                </button>
                            </form>
                            <a href="{{ route('checkout.show') }}" class="flex items-center gap-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition transform hover:scale-105">
                                Proceed to Checkout
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-xl p-16 text-center">
                    <svg class="w-32 h-32 text-gray-300 mx-auto mb-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/>
                    </svg>
                    <p class="text-gray-500 text-2xl mb-2 font-semibold">Your cart is empty</p>
                    <p class="text-gray-400 mb-8">Start adding some awesome products!</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition transform hover:scale-105">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                        </svg>
                        Continue Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        const formatCurrency = (value) => {
            return 'Rp ' + Number(value).toLocaleString('id-ID');
        };

        const updateTotals = () => {
            let total = 0;
            document.querySelectorAll('.cart-quantity').forEach(input => {
                const quantity = Math.max(parseInt(input.value, 10) || 1, 1);
                const price = parseInt(input.closest('tr').querySelector('.cart-item-subtotal').dataset.price, 10);
                const subtotal = price * quantity;
                input.closest('tr').querySelector('.cart-item-subtotal').textContent = formatCurrency(subtotal);
                total += subtotal;
            });
            const totalLabel = document.getElementById('cart-total');
            if (totalLabel) {
                totalLabel.textContent = formatCurrency(total);
            }
        };

        const syncQuantity = (input) => {
            const cartId = input.dataset.cartId;
            const quantity = Math.max(parseInt(input.value, 10) || 1, 1);
            input.value = quantity;

            fetch(`/cart/${cartId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateTotals();
                } else {
                    alert(data.error);
                }
            });
        };

        document.querySelectorAll('.cart-quantity').forEach(input => {
            input.addEventListener('input', () => {
                if (parseInt(input.value, 10) < 1) {
                    input.value = 1;
                }
                updateTotals();
            });

            input.addEventListener('blur', () => syncQuantity(input));
            input.addEventListener('change', () => syncQuantity(input));
        });

        updateTotals();
    </script>
    @endpush
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white">
            {{ __('Secure Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8">
                    <div id="alert-container"></div>

                    <form id="checkout-form" class="space-y-8">
                        @csrf
                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
                        <input type="hidden" name="device_hash" value="{{ md5(request()->ip() . request()->userAgent()) }}">

                        <!-- Order Items -->
                        <div>
                            <div class="flex items-center mb-6">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-800">Order Items</h3>
                            </div>
                            <div id="items-container" class="space-y-4">
                                @forelse($cartItems as $index => $cartItem)
                                    <div class="item-row bg-gradient-to-r from-blue-50 to-indigo-50 p-5 rounded-xl border border-blue-200">
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name</label>
                                                <input type="text" name="items[{{ $index }}][product_name]" required readonly
                                                       class="border-gray-300 rounded-lg shadow-sm w-full bg-white font-semibold"
                                                       value="{{ $cartItem->product->name }}">
                                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $cartItem->product_id }}">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Unit Price (Rp)</label>
                                                <input type="number" name="items[{{ $index }}][unit_price]" required min="0" readonly
                                                       class="item-price border-gray-300 rounded-lg shadow-sm w-full bg-white font-semibold text-blue-600"
                                                       value="{{ $cartItem->product->price }}">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                                                <input type="number" name="items[{{ $index }}][qty]" required min="1"
                                                       class="item-qty border-2 border-blue-300 rounded-lg shadow-sm w-full focus:border-blue-500 focus:ring focus:ring-blue-200 font-semibold"
                                                       value="{{ $cartItem->quantity }}">
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-xl">
                                        <p class="text-yellow-800 font-semibold">Your cart is empty. <a href="{{ route('products.index') }}" class="underline text-blue-600">Add some products</a> first.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Voucher Section -->
                        <div class="border-t-2 border-gray-200 pt-8">
                            <div class="flex items-center mb-6">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/>
                                    <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-800">Voucher Code (Optional)</h3>
                            </div>
                            <div class="flex gap-3">
                                <input type="text" id="voucher-code" name="voucher_code"
                                       class="border-2 border-gray-300 rounded-lg shadow-sm flex-1 focus:border-blue-500 focus:ring focus:ring-blue-200 font-semibold uppercase"
                                       placeholder="Enter voucher code (e.g., SRIFOTON10)">
                                <button type="button" id="validate-voucher"
                                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg hover:shadow-xl transition">
                                    Validate
                                </button>
                            </div>
                            <div id="voucher-status" class="mt-3 text-sm font-semibold"></div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="border-t-2 border-gray-200 pt-8">
                            <div class="flex items-center mb-6">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h7a2 2 0 011.732 1H16a2 2 0 011.995 1.85L18 6v1.382a2 2 0 01-.336 1.11l-1.5 2.25a2 2 0 01-1.664.856H12v1.5a1.5 1.5 0 01-1.356 1.493L10.5 14H7a2 2 0 01-1.994-1.85L5 12V7H3a1 1 0 01-.117-1.993L3 5z" clip-rule="evenodd" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-800">Shipping Method</h3>
                            </div>
                            <div class="space-y-4">
                                <label class="flex items-start p-5 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-500 transition">
                                    <input type="radio" name="shipping_method" value="standard" required class="mr-4 mt-1 w-5 h-5 text-blue-600" checked>
                                    <div>
                                        <div class="font-bold text-gray-800">Standard Courier (3-5 days)</div>
                                        <div class="text-sm text-gray-600">Reliable delivery with tracking</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-5 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-500 transition">
                                    <input type="radio" name="shipping_method" value="express" class="mr-4 mt-1 w-5 h-5 text-blue-600">
                                    <div>
                                        <div class="font-bold text-gray-800">Express Courier (1-2 days)</div>
                                        <div class="text-sm text-gray-600">Priority handling and fast arrival</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-5 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-500 transition">
                                    <input type="radio" name="shipping_method" value="same_day" class="mr-4 mt-1 w-5 h-5 text-blue-600">
                                    <div>
                                        <div class="font-bold text-gray-800">Same Day Delivery</div>
                                        <div class="text-sm text-gray-600">Available for selected areas before 12 PM</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="border-t-2 border-gray-200 pt-8">
                            <div class="flex items-center mb-6">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-800">Shipping Address</h3>
                            </div>
                            <textarea name="shipping_address" rows="4" required
                                      class="border-2 border-gray-300 rounded-lg shadow-sm w-full focus:border-blue-500 focus:ring focus:ring-blue-200 font-medium"
                                      placeholder="Enter full shipping address (Street, City, Province, Postal Code)"></textarea>
                        </div>

                        <!-- Payment Method -->
                        <div class="border-t-2 border-gray-200 pt-8">
                            <div class="flex items-center mb-6">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-800">Payment Method</h3>
                            </div>
                            <div class="space-y-4">
                                <label class="flex items-center p-5 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-500 transition">
                                    <input type="radio" name="payment_method" value="bank_transfer" required class="mr-4 w-5 h-5 text-blue-600">
                                    <div class="flex items-center">
                                        <svg class="w-10 h-10 text-blue-600 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <div class="font-bold text-gray-800">Bank Transfer</div>
                                            <div class="text-sm text-gray-600">Transfer to our bank account</div>
                                        </div>
                                    </div>
                                </label>
                                <label class="flex items-center p-5 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-500 transition">
                                    <input type="radio" name="payment_method" value="cod" class="mr-4 w-5 h-5 text-blue-600">
                                    <div class="flex items-center">
                                        <svg class="w-10 h-10 text-green-600 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <div class="font-bold text-gray-800">Cash on Delivery (COD)</div>
                                            <div class="text-sm text-gray-600">Pay when item is delivered</div>
                                        </div>
                                    </div>
                                </label>
                                <label class="flex items-center p-5 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-500 transition">
                                    <input type="radio" name="payment_method" value="e_wallet" class="mr-4 w-5 h-5 text-blue-600">
                                    <div class="flex items-center">
                                        <svg class="w-10 h-10 text-purple-600 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <div class="font-bold text-gray-800">E-Wallet</div>
                                            <div class="text-sm text-gray-600">OVO, GoPay, DANA, etc.</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="border-t-2 border-gray-200 pt-8">
                            <div class="flex items-center mb-6">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-800">Order Summary</h3>
                            </div>
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border-2 border-blue-200 space-y-3">
                                <div class="flex justify-between text-lg">
                                    <span class="text-gray-700 font-semibold">Subtotal:</span>
                                    <span class="font-bold text-gray-800" id="subtotal">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-green-600 text-lg" id="discount-row" style="display: none;">
                                    <span class="font-semibold">Discount:</span>
                                    <span class="font-bold" id="discount">- Rp 0</span>
                                </div>
                                <div class="flex justify-between text-2xl font-bold border-t-2 border-blue-300 pt-3">
                                    <span class="text-gray-800">Total:</span>
                                    <span class="text-blue-600" id="total">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                    class="flex items-center gap-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold px-10 py-4 rounded-xl shadow-lg hover:shadow-xl transition transform hover:scale-105">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                Process Secure Checkout
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemCount = 1;
        let voucherData = null;

        // Calculate total
        function calculateTotal() {
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                subtotal += price * qty;
            });

            let discount = 0;
            if (voucherData && voucherData.valid) {
                if (voucherData.type === 'percentage') {
                    discount = Math.floor(subtotal * voucherData.value / 100);
                } else {
                    discount = voucherData.value;
                }
                discount = Math.min(discount, subtotal);
            }

            const total = Math.max(subtotal - discount, 0);

            document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('discount').textContent = '- Rp ' + discount.toLocaleString('id-ID');
            document.getElementById('total').textContent = 'Rp ' + total.toLocaleString('id-ID');
            
            document.getElementById('discount-row').style.display = discount > 0 ? 'flex' : 'none';
        }

        // Listen to price/qty changes
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('item-price') || e.target.classList.contains('item-qty')) {
                calculateTotal();
            }
        });

        // Validate voucher
        document.getElementById('validate-voucher').addEventListener('click', async function() {
            const code = document.getElementById('voucher-code').value.trim();
            if (!code) {
                showVoucherStatus('Please enter a voucher code', 'error');
                return;
            }

            const subtotal = Array.from(document.querySelectorAll('.item-row')).reduce((sum, row) => {
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                return sum + (price * qty);
            }, 0);

            try {
                const response = await fetch('/api/vouchers/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        code: code,
                        order_amount: subtotal
                    })
                });

                const data = await response.json();
                
                if (data.valid) {
                    voucherData = data;
                    showVoucherStatus(`✓ Voucher applied! You save Rp ${data.discount_amount.toLocaleString('id-ID')}`, 'success');
                    calculateTotal();
                } else {
                    voucherData = null;
                    showVoucherStatus(`✗ ${data.reason || 'Invalid voucher'}`, 'error');
                    calculateTotal();
                }
            } catch (error) {
                showVoucherStatus('Error validating voucher', 'error');
            }
        });

        function showVoucherStatus(message, type) {
            const statusDiv = document.getElementById('voucher-status');
            statusDiv.textContent = message;
            statusDiv.className = `mt-2 text-sm ${type === 'success' ? 'text-green-600' : 'text-red-600'}`;
        }

        // Form submission
        document.getElementById('checkout-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {};
            
            // Convert FormData to JSON
            formData.forEach((value, key) => {
                if (key.startsWith('items[')) {
                    if (!data.items) data.items = [];
                    const match = key.match(/items\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = parseInt(match[1]);
                        const field = match[2];
                        if (!data.items[index]) data.items[index] = {};
                        data.items[index][field] = field === 'product_name' ? value : parseInt(value) || value;
                    }
                } else {
                    data[key] = value;
                }
            });

            try {
                const response = await fetch('{{ route("checkout.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData.get('_token')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    showAlert('Order created successfully! Redirecting to your order history...', 'success');
                    setTimeout(() => {
                        const redirectUrl = result.redirect_url ?? '{{ route('orders.index') }}';
                        window.location.href = redirectUrl;
                    }, 1500);
                } else {
                    showAlert(result.message || 'Checkout failed', 'error');
                }
            } catch (error) {
                showAlert('Error processing checkout', 'error');
            }
        });

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `p-4 mb-4 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            alertDiv.textContent = message;
            document.getElementById('alert-container').appendChild(alertDiv);
            
            setTimeout(() => alertDiv.remove(), 5000);
        }

        // Initial calculation
        calculateTotal();
    </script>
</x-app-layout>

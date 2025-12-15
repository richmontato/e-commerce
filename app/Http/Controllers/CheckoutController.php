<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Show checkout page
     */
    public function showCheckout(): View
    {
        $cartItems = auth()->user()->carts()->with('product')->get();
        return view('checkout', compact('cartItems'));
    }

    /**
     * Process checkout
     */
    public function processCheckout(CheckoutRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->validated();
        $user    = $request->user();

        $idem = hash('sha256', (string) $payload['idempotency_key']); // cast ke string

        if ($existing = Order::where('idempotency_key', $idem)->first()) {
            return response()->json([
                'order_id' => $existing->id,
                'subtotal' => (int) $existing->subtotal_amount,
                'discount' => (int) $existing->discount_amount,
                'total'    => (int) $existing->total_amount,
                'status'   => $existing->status,
                'shipping_method' => $existing->shipping_method,
                'shipping_method_label' => $existing->shipping_method_label,
                'redirect_url' => route('orders.index'),
            ], 200);
        }

        /** @var array<int, array<string, mixed>> $items */
        $items = $payload['items'] ?? [];

        $subtotal = collect($items)
            ->sum(fn(array $it) => (int) $it['unit_price'] * (int) $it['qty']);

        $discount = 0;
        $voucher  = null;

        return DB::transaction(function () use ($user, $payload, $subtotal, $idem, &$discount, &$voucher, $items) {
            if (!empty($payload['voucher_code'])) {
                $voucher = Voucher::where('code', strtoupper((string) $payload['voucher_code']))
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (!$voucher) {
                    abort(400, 'Invalid voucher');
                }

                // Use timezone aware comparison
                $now = now()->setTimezone(config('app.timezone', 'Asia/Jakarta'));
                
                if ($voucher->starts_at) {
                    $startsAt = \Carbon\Carbon::parse($voucher->starts_at)->setTimezone(config('app.timezone', 'Asia/Jakarta'));
                    if ($now->lt($startsAt)) {
                        abort(400, 'Voucher not started');
                    }
                }
                
                if ($voucher->ends_at) {
                    $endsAt = \Carbon\Carbon::parse($voucher->ends_at)->setTimezone(config('app.timezone', 'Asia/Jakarta'));
                    if ($now->gt($endsAt)) {
                        abort(400, 'Voucher expired');
                    }
                }
                if ($subtotal < (int) $voucher->min_order_amount) {
                    abort(400, 'Min order not met');
                }

                if ($voucher->max_uses > 0) {
                    $used = VoucherRedemption::where('voucher_id', $voucher->id)
                        ->lockForUpdate()->count();
                    if ($used >= $voucher->max_uses) {
                        abort(400, 'Voucher quota exhausted');
                    }
                }

                if ($user && $voucher->per_user_limit > 0) {
                    $userUsed = VoucherRedemption::where('voucher_id', $voucher->id)
                        ->where('user_id', $user->id)
                        ->lockForUpdate()->count();
                    if ($userUsed >= $voucher->per_user_limit) {
                        abort(400, 'Voucher per-user limit reached');
                    }
                }

                $discount = $voucher->type === 'percentage'
                    ? intdiv($subtotal * min((int) $voucher->value, 100), 100)
                    : (int) $voucher->value;
            }

            $total = max($subtotal - $discount, 0);

            $order = Order::create([
                'user_id'         => $user?->id,
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'total_amount'    => $total,
                'currency'        => 'IDR',
                'status'          => 'pending',
                'voucher_code'    => $voucher?->code,
                'idempotency_key' => $idem,
                'shipping_address'=> $payload['shipping_address'] ?? null,
                'shipping_method' => $payload['shipping_method'],
                'payment_method'  => $payload['payment_method'] ?? null,
            ]);

            foreach ($items as $it) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $it['product_id'] ?? null,
                    'product_name' => (string) $it['product_name'],
                    'unit_price'   => (int) $it['unit_price'],
                    'qty'          => (int) $it['qty'],
                    'line_total'   => (int) $it['unit_price'] * (int) $it['qty'],
                ]);
            }

            if ($voucher) {
                VoucherRedemption::create([
                    'voucher_id' => $voucher->id,
                    'user_id'    => $user?->id,
                    'order_id'   => $order->id,
                    'device_hash'=> $payload['device_hash'] ?? null,
                    'used_at'    => now(),
                ]);
            }

            return response()->json([
                'order_id' => $order->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total'    => $total,
                'status'   => $order->status,
                'shipping_method' => $order->shipping_method,
                'shipping_method_label' => $order->shipping_method_label,
                'redirect_url' => route('orders.index'),
            ], 201);
        });
    }

    /**
     * Print order
     */
    public function printOrder(Order $order): View
    {
        // Load relationships
        $order->load('items', 'user');
        
        // Authorize: user can only print their own order
        if ($order->user_id && auth()->id() !== $order->user_id) {
            abort(403, 'Unauthorized');
        }
        
        return view('orders.print', compact('order'));
    }
}

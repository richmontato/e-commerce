<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan.
     */
    public function index(): View
    {
        $user = auth()->user();

        if ($user?->isAdmin()) {
            $orders = Order::with(['user', 'items.product'])
                ->latest()
                ->paginate(10);

            return view('orders.index_admin', [
                'orders' => $orders,
            ]);
        }

        if ($user?->isSeller()) {
            $orders = Order::forSeller((int) $user->id)
                ->with([
                    'user',
                    'items' => function ($query) use ($user): void {
                        $query->whereHas('product', function ($builder) use ($user): void {
                                $builder->where('user_id', $user->id);
                            })
                            ->with(['product' => function ($builder) use ($user): void {
                                $builder->where('user_id', $user->id);
                            }]);
                    },
                ])
                ->latest()
                ->paginate(10);

            return view('orders.index_seller', [
                'orders' => $orders,
            ]);
        }

        $orders = Order::with(['items.product'])
            ->where('user_id', $user?->id)
            ->latest()
            ->paginate(10);

        return view('orders.index_customer', [
            'orders' => $orders,
        ]);
    }

    /**
     * Menampilkan detail satu pesanan.
     * Relasi 'items.product' dan 'user' sudah dimuat otomatis
     * berkat Route Model Binding.
     */
    public function show(Order $order): View
    {
        $user = auth()->user();

        $canView = false;

        if ($user?->isAdmin()) {
            $canView = true;
        } elseif ($order->user_id === $user?->id) {
            $canView = true;
        } elseif ($user?->isSeller() && $order->sellerHasAccess((int) $user->id)) {
            $canView = true;
        }

        if (!$canView) {
            abort(403, 'Unauthorized to view this order');
        }

        // Eager load relasi untuk ditampilkan di view
        $order->loadMissing('items.product', 'user');

        $visibleItems = $order->items;

        if ($user?->isSeller() && !$user->isAdmin()) {
            $visibleItems = $order->items
                ->filter(function ($item) use ($user) {
                    if (!$item->product) {
                        return false;
                    }

                    return (int) $item->product->user_id === (int) $user->id;
                })
                ->values();
        }

        $canManageStatus = false;

        if ($user?->isAdmin()) {
            $canManageStatus = true;
        } elseif ($user?->isSeller() && $order->sellerHasAccess((int) $user->id)) {
            $canManageStatus = true;
        }

        return view('orders.show', [
            'order' => $order,
            'visibleItems' => $visibleItems,
            'viewerRole' => $user?->role,
            'canUpdateStatus' => $canManageStatus,
            'statusTransitions' => $order->allowedStatusTransitions(),
        ]);
    }

    /**
     * Memperbarui status pesanan.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $user = $request->user();

        $hasPermission = $user?->isAdmin() || ($user?->isSeller() && $order->sellerHasAccess((int) $user->id));

        if (!$hasPermission) {
            abort(403, 'You are not allowed to update this order.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,delivered,canceled'],
        ]);

        $allowedTransitions = $order->allowedStatusTransitions();

        if (!in_array($validated['status'], $allowedTransitions, true)) {
            return redirect()
                ->back()
                ->withErrors(['status' => 'Status tidak valid untuk kondisi pesanan saat ini.'])
                ->withInput();
        }

        $order->update(['status' => $validated['status']]);

        return redirect()->route('orders.show', $order)
                         ->with('success', 'Order status updated successfully.');
    }

    /**
     * Menghapus pesanan beserta itemnya.
     */
    public function destroy(Order $order): RedirectResponse
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Only administrators can delete orders');
        }

        $order->delete();

        return redirect()->route('orders.index')
                         ->with('success', 'Order deleted successfully.');
    }

    /**
     * Mencetak invoice untuk pesanan tertentu.
     */
    public function print(Order $order): View
    {
        // Users can only print their own orders, admin can print all
        if (!auth()->user()?->isAdmin() && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized to print this order');
        }

        $order->loadMissing('items.product', 'user');
        return view('orders.print', compact('order'));
    }
}
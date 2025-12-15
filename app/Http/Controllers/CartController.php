<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Display the user's shopping cart
     */
    public function index(): View
    {
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    /**
     * Add product to cart
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check stock
        if ($product->stock < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock available.');
        }

        $cart = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cart) {
            // Update existing cart item
            $newQuantity = $cart->quantity + $validated['quantity'];
            if ($product->stock < $newQuantity) {
                return back()->with('error', 'Insufficient stock available.');
            }
            $cart->update(['quantity' => $newQuantity]);
        } else {
            // Create new cart item
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
            ]);
        }

        return back()->with('success', 'Product added to cart successfully.');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        // Check stock
        if ($cart->product->stock < $validated['quantity']) {
            return response()->json([
                'error' => 'Insufficient stock available.',
                'max_stock' => $cart->product->stock
            ], 400);
        }

        $cart->update(['quantity' => $validated['quantity']]);

        $subtotal = $cart->product->price * $cart->quantity;
        $totalCart = Cart::where('user_id', auth()->id())
            ->with('product')
            ->get()
            ->sum(fn($item) => $item->product->price * $item->quantity);

        return response()->json([
            'success' => true,
            'subtotal' => $subtotal,
            'total_cart' => $totalCart,
        ]);
    }

    /**
     * Remove cart item
     */
    public function destroy(Cart $cart): RedirectResponse
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cart->delete();

        return redirect()->route('cart.index')
            ->with('success', 'Item removed from cart.');
    }

    /**
     * Clear all cart items
     */
    public function clear(): RedirectResponse
    {
        Cart::where('user_id', auth()->id())->delete();

        return redirect()->route('cart.index')
            ->with('success', 'Cart cleared successfully.');
    }
}

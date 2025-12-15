<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;

class FakeOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Find the user or create them if they don't exist
        $user = User::firstOrCreate(
            ['email' => 'john.doe@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SELLER,
                'phone_number' => '081234567890',
                'email_verified_at' => now(),
            ]
        );

        if ($user->role !== User::ROLE_SELLER) {
            $user->forceFill([
                'role' => User::ROLE_SELLER,
                'phone_number' => $user->phone_number ?? '081234567890',
            ])->save();
        }

        // 2. Find the product or create it if it doesn't exist
        $product = Product::firstOrCreate(
            ['name' => 'Sample T-Shirt'],
            [
                'user_id' => $user->id,
                'slug' => 'sample-t-shirt',
                'description' => 'A high-quality cotton t-shirt.',
                'price' => 2550, // price in cents
                'stock' => 100,
                'category' => 'Clothing',
                'is_active' => true,
            ]
        );

        // 3. Create a new order for the user
        // We still use create() here because we want a new order each time we seed
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'subtotal_amount' => 5100, // 51.00 in cents
            'discount_amount' => 0,
            'total_amount' => 5100,
            'currency' => 'IDR',
            'payment_method' => 'bank_transfer',
            'shipping_address' => "123 Main St\nAnytown, USA 12345",
            'shipping_method' => 'standard',
        ]);

        // 4. Create an order item and attach it to the order
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'qty' => 2,
            'line_total' => $product->price * 2,
        ]);
    }
}
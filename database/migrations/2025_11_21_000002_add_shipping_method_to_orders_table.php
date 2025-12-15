<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (!Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method', 32)->default('standard')->after('shipping_address');
                $table->index('shipping_method');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'shipping_method')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->dropIndex('orders_shipping_method_index');
                $table->dropColumn('shipping_method');
            });
        }
    }
};

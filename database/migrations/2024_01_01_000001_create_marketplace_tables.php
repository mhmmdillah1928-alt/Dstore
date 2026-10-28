<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->string('phone', 20)->nullable();
            $t->enum('role', ['admin', 'seller', 'customer'])->default('customer');
            $t->rememberToken();
            $t->timestamps();
        });

        Schema::create('stores', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('store_name');
            $t->string('logo')->nullable();
            $t->text('address');
            $t->boolean('is_verified')->default(false);
            $t->decimal('balance', 15, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('brands', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('store_id')->constrained()->cascadeOnDelete();
            $t->foreignId('brand_id')->constrained();
            $t->string('name');
            $t->string('slug')->unique();
            $t->enum('condition', ['new', 'used'])->default('new');
            $t->decimal('price', 15, 2);
            $t->unsignedInteger('stock')->default(0);
            $t->unsignedInteger('weight')->default(0)->comment('gram');
            $t->unsignedSmallInteger('ram')->comment('GB');
            $t->unsignedSmallInteger('storage')->comment('GB');
            $t->text('description')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('product_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('image_path');
            $t->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('qty')->default(1);
            $t->timestamps();
            $t->unique(['user_id', 'product_id']);
        });

        // Satu order = satu toko (checkout multi-toko dipecah per toko)
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('order_number')->unique();
            $t->foreignId('customer_id')->constrained('users');
            $t->foreignId('store_id')->constrained();
            $t->decimal('total_amount', 15, 2);
            $t->decimal('commission_amount', 15, 2)->default(0);
            $t->text('shipping_address');
            $t->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $t->enum('order_status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $t->timestamps();
        });

        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained();
            $t->foreignId('store_id')->constrained();
            $t->unsignedInteger('qty');
            $t->decimal('price', 15, 2);
            $t->timestamps();
        });

        Schema::create('shipments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->string('courier_name');
            $t->string('tracking_number');
            $t->decimal('shipping_cost', 15, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('withdrawals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('store_id')->constrained()->cascadeOnDelete();
            $t->decimal('amount', 15, 2);
            $t->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $t->timestamps();
        });

        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('rating');
            $t->text('comment')->nullable();
            $t->timestamps();
            $t->unique('order_item_id');
        });

        Schema::create('disputes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->text('reason');
            $t->enum('status', ['open', 'refunded', 'rejected'])->default('open');
            $t->text('admin_note')->nullable();
            $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->string('value');
        });
    }

    public function down(): void
    {
        foreach (['settings', 'disputes', 'reviews', 'withdrawals', 'shipments', 'order_items', 'orders', 'cart_items', 'product_images', 'products', 'brands', 'stores', 'users'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};

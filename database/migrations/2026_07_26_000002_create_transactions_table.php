<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('shipping_address');
            $table->string('shipping_courier');
            $table->decimal('latitude', 10, 8)->nullable(); // Sensor GPS Coordinate
            $table->decimal('longitude', 11, 8)->nullable(); // Sensor GPS Coordinate
            $table->decimal('total_price', 12, 2);
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->string('payment_proof')->nullable(); // Path to the uploaded receipt
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable for guest checkouts
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

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
        Schema::create('shoes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->string('category'); // e.g. Running, Casual, Basketball, Sneakers
            $table->decimal('price', 12, 2);
            $table->json('sizes'); // JSON array of sizes e.g. [39, 40, 41, 42, 43, 44]
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shoes');
    }
};

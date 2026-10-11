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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('item_name');
            $table->string('category'); // e.g., Metal, Acrylic, Chemical, Wood
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('unit'); // e.g., sheets, liters, pieces, kg
            $table->decimal('low_stock_threshold', 10, 2)->default(10); // Triggers the low stock warning
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

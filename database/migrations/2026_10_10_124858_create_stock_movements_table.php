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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id('stock_movement_id');
            $table->foreignId('material_id')->constrained('materials', 'material_id')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders', 'order_id')->nullOnDelete();
            $table->string('movement_type'); // IN, OUT, REUSE
            $table->integer('quantity');
            $table->date('movement_date');
            $table->string('movement_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id('delivery_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->restrictOnDelete();
            $table->date('delivery_date')->nullable();
            $table->string('delivery_status')->default('Pending'); // Pending, Out for Delivery, Delivered
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};

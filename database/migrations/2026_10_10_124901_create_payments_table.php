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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->restrictOnDelete();
            $table->string('payment_type'); // Downpayment (50%), Final (50%), Full
            $table->string('payment_method'); // Cash, Bank Transfer, GCash
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('reference_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

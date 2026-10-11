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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('client_id')->constrained('clients', 'client_id')->restrictOnDelete();
            $table->string('po_number')->unique();
            $table->date('order_date');
            $table->date('due_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('order_status')->default('Pending Downpayment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

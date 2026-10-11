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
        Schema::create('prototypes', function (Blueprint $table) {
            $table->id('prototype_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->cascadeOnDelete();
            $table->date('prototype_date');
            $table->string('approval_status')->default('Pending'); // Pending, Approved, Disapproved
            $table->date('approval_date')->nullable();
            $table->text('client_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prototypes');
    }
};

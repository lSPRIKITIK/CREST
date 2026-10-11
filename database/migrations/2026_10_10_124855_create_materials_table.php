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
        Schema::create('materials', function (Blueprint $table) {
            $table->id('material_id');
            $table->string('material_name');
            $table->string('material_type'); // Wood, Acrylic, Metal
            $table->string('unit'); // pcs, sheets, kg, etc.
            $table->decimal('unit_cost', 10, 2);
            $table->integer('quantity_on_hand')->default(0);
            $table->integer('reorder_level')->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};

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
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id('sales_report_id');
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->restrictOnDelete();
            $table->string('report_type'); // Monthly, Yearly
            $table->string('period_covered');
            $table->decimal('total_sales', 14, 2);
            $table->date('generated_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_reports');
    }
};

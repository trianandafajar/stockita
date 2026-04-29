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

            $table->string('invoice_code')->unique();
            // $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->integer('paid');
            $table->integer('change');

            $table->decimal('total', 12, 2)->default(0);

            $table->enum('status', ['pending', 'paid'])->default('paid');

            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();

            $table->string('receipt')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

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

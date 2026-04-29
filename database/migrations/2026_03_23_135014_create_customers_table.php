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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('type', ['regular', 'exclusive'])->default('regular');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('store_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->timestamp('last_contacted')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

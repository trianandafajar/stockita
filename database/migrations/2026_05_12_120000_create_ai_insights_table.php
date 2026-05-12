<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->json('payload');
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index(['store_id', 'user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};

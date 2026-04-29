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
        Schema::table('subscriptions', function (Blueprint $table) {

            $table->foreignId('plan_id')
                ->after('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('interval', ['monthly', 'yearly'])->change();

            $table->enum('status', ['active', 'expired', 'cancelled'])->change();
            $table->dropColumn('plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

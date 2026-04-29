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
        Schema::table('midtrans_transactions', function (Blueprint $table) {

            // tambah plan_id
            $table->foreignId('plan_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->cascadeOnDelete();

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

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetDemoData extends Command
{
    protected $signature   = 'demo:reset {--force : Jalankan tanpa konfirmasi}';
    protected $description = 'Reset semua data demo ke kondisi awal';

    public function handle(): void
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  Yakin ingin reset semua data demo?')) {
                $this->info('Dibatalkan.');
                return;
            }
        }

        $this->info('Memulai reset data demo...');

        try {
            DB::transaction(function () {

                $this->info('Menghapus data demo lama...');

                $demoOrderIds = DB::table('transactions')
                    ->where('is_demo', true)
                    ->pluck('id');

                DB::table('transactions')->where('is_demo', true)->delete();
                DB::table('transaction_items')->whereIn('transaction_id', $demoOrderIds)->delete();
                DB::table('stocks')->where('is_demo', true)->delete();
                DB::table('products')->where('is_demo', true)->delete();
                DB::table('categories')->where('is_demo', true)->delete();
                DB::table('warehouses')->where('is_demo', true)->delete();
                DB::table('customers')->where('is_demo', true)->delete();
                DB::table('stores')->where('is_demo', true)->delete();
                DB::table('subscriptions')->where('is_demo', true)->delete();

                $this->info('Mengisi ulang data demo...');

                Artisan::call('db:seed', [
                    '--class' => 'DemoDataSeeder',
                    '--force' => true,
                ]);
            });

            $this->info('Reset data demo berhasil! — ' . now()->format('d/m/Y H:i:s'));
            Log::info('[DEMO] Reset berhasil pada ' . now());
        } catch (\Exception $e) {
            $this->error('Gagal: ' . $e->getMessage());
            Log::error('[DEMO] Reset gagal: ' . $e->getMessage());
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        EmailTemplate::updateOrCreate(
            ['key' => 'welcome_email'],
            [
                'subject' => 'Selamat Datang di {{ store_name }}',
                'body' => "
                    <p style='margin-bottom:16px;'>Halo <strong>{{ name }}</strong>,</p>

                    <p style='margin-bottom:16px;'>
                        Selamat datang di <strong>{{ store_name }}</strong>.
                        Kami menghargai kepercayaan Anda untuk bergabung bersama kami.
                    </p>

                    <p style='margin-bottom:16px;'>
                        Akun Anda telah berhasil dibuat dan siap digunakan.
                        Anda kini dapat mengakses berbagai fitur yang kami sediakan
                        untuk mendukung aktivitas Anda secara lebih efisien.
                    </p>

                    <p style='margin-top:24px;'>
                        Jika Anda membutuhkan bantuan, tim kami siap membantu kapan saja.
                    </p>

                    <p style='margin-top:24px;'>
                        Hormat kami,<br>
                        <strong>Tim {{ store_name }}</strong>
                    </p>
                "
            ]
        );

        EmailTemplate::updateOrCreate(
            ['key' => 'out_of_stock'],
            [
                'subject' => 'Pemberitahuan Stok Habis: {{ product_name }}',
                'body' => "
                    <p style='margin-bottom:16px;'>Halo <strong>{{ name }}</strong>,</p>

                    <p style='margin-bottom:16px;'>
                        Kami ingin memberitahukan bahwa produk berikut saat ini sudah habis:
                    </p>

                    <div style='margin:20px 0; padding:16px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px;'>
                        <p style='margin:0; font-weight:600;'>{{ product_name }}</p>
                        <p style='margin:4px 0 0 0; font-size:13px; color:#6b7280;'>
                            Kode: {{ product_code }}
                        </p>
                    </div>

                    <p style='margin-bottom:16px;'>
                        Kami menyarankan untuk segera melakukan restock agar tidak mengganggu penjualan.
                    </p>

                    <div style='margin:25px 0; text-align:center;'>
                        <a href='{{ warehouse_url }}'
                        style='background:#ef4444; color:#ffffff; padding:12px 24px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; display:inline-block;'>
                        Kelola Stok
                        </a>
                    </div>

                    <p style='margin-top:24px;'>
                        Terima kasih atas perhatian Anda.
                    </p>

                    <p style='margin-top:24px;'>
                        Hormat kami,<br>
                        <strong>Tim {{ store_name }}</strong>
                    </p>
                "
            ]
        );

        EmailTemplate::updateOrCreate(
            [
                'key' => 'transaction.success'
            ],

            [
                'subject' => 'Pembayaran Berhasil - {{ transaction.code }}',
                'body' => "
                <p>Halo <strong>{{ user.name }}</strong>,</p>

                <p>
                Pembayaran Anda telah berhasil diproses.
                </p>

                <p>
                Berikut detail transaksi:
                </p>

                <ul>
                    <li>Kode Transaksi: <strong>{{ transaction.code }}</strong></li>
                    <li>Tanggal: <strong>{{ transaction.date }}</strong></li>
                    <li>Total: <strong>Rp {{ transaction.total }}</strong></li>
                    <li>Status: <strong>{{ transaction.status }}</strong></li>
                </ul>

                <p>
                Terima kasih telah bertransaksi di <strong>{{ store.name }}</strong>.
                </p>

                <p>
                    <a href='{{ url.invoice }}' style='color:#10b981; font-weight:bold; text-decoration:none;'>
                    Lihat Detail Transaksi →
                    </a>
                </p>
            "
            ]
        );
    }
}

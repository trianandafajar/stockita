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
        // 1. Welcome Email Template
        EmailTemplate::updateOrCreate(
            ['key' => 'welcome_email'],
            [
                'subject' => 'Welcome to {{ store_name }}!',
                'body' => "
                    <p style='margin-bottom:16px;'>Hi <strong>{{ name }}</strong>,</p>

                    <p style='margin-bottom:16px;'>
                        Welcome to <strong>{{ store_name }}</strong>. 
                        We are thrilled to have you on board and appreciate your trust in us.
                    </p>

                    <p style='margin-bottom:16px;'>
                        Your account has been successfully created and is ready to use. 
                        You can now access all the features we provide to support your activities more efficiently.
                    </p>

                    <p style='margin-top:24px;'>
                        If you ever need any assistance, our team is here to help you anytime.
                    </p>

                    <p style='margin-top:24px;'>
                        Best regards,<br>
                        <strong>The {{ store_name }} Team</strong>
                    </p>
                "
            ]
        );

        // 2. Out of Stock Template
        EmailTemplate::updateOrCreate(
            ['key' => 'out_of_stock'],
            [
                'subject' => 'Out of Stock Alert: {{ product_name }}',
                'body' => "
                    <p style='margin-bottom:16px;'>Hello <strong>{{ name }}</strong>,</p>

                    <p style='margin-bottom:16px;'>
                        This is an automated notification to inform you that the following product is currently out of stock:
                    </p>

                    <div style='margin:20px 0; padding:16px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px;'>
                        <p style='margin:0; font-weight:600;'>{{ product_name }}</p>
                        <p style='margin:4px 0 0 0; font-size:13px; color:#6b7280;'>
                            Code: {{ product_code }}
                        </p>
                    </div>

                    <p style='margin-bottom:16px;'>
                        We recommend restocking this item soon to avoid any disruption in your sales.
                    </p>

                    <div style='margin:25px 0; text-align:center;'>
                        <a href='{{ warehouse_url }}' 
                        style='background:#ef4444; color:#ffffff; padding:12px 24px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; display:inline-block;'>
                        Manage Inventory
                        </a>
                    </div>

                    <p style='margin-top:24px;'>
                        Thank you for your attention.
                    </p>

                    <p style='margin-top:24px;'>
                        Best regards,<br>
                        <strong>The {{ store_name }} Team</strong>
                    </p>
                "
            ]
        );

        // 3. Transaction Success Template
        EmailTemplate::updateOrCreate(
            ['key' => 'transaction.success'],
            [
                'subject' => 'Payment Successful - {{ transaction.code }}',
                'body' => "
                <p>Hi <strong>{{ user.name }}</strong>,</p>

                <p>
                    Your payment has been processed successfully.
                </p>

                <p>
                    Here are your transaction details:
                </p>

                <ul>
                    <li>Transaction Code: <strong>{{ transaction.code }}</strong></li>
                    <li>Date: <strong>{{ transaction.date }}</strong></li>
                    <li>Total Amount: <strong>{{ transaction.total }}</strong></li>
                    <li>Status: <strong>{{ transaction.status }}</strong></li>
                </ul>

                <p>
                    Thank you for shopping at <strong>{{ store.name }}</strong>.
                </p>

                <p>
                    <a href='{{ url.invoice }}' style='color:#10b981; font-weight:bold; text-decoration:none;'>
                    View Transaction Details →
                    </a>
                </p>
            "
            ]
        );
    }
}

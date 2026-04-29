<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OutOfStockMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contentData;
    public $subjectText;
    public $storeName;
    public $warehouse_url;

    public function __construct($data)
    {
        $email = email_template('out_of_stock');

        $template = $email?->body ?? 'Stok produk habis';
        $subject = $email?->subject ?? 'Stok Habis';

        $global = [
            'app_url' => config('app.url'),
            'year' => date('Y'),
        ];

        $finalData = array_merge([
            'app_url' => config('app.url'),
            'year' => date('Y'),
            'warehouse_url' => config('app.url') . '/warehouse/' . $data['warehouse_id'],
        ], $data);

        $this->contentData = parse_template($template, $finalData);
        $this->subjectText = parse_template($subject, $finalData);
        $this->storeName = $finalData['store_name'] ?? 'Toko';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dynamic',
            with: [
                'content' => $this->contentData,
                'store_name' => $this->storeName,
                'warehouse_url' => $this->warehouse_url,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

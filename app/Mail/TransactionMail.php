<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionMail extends Mailable
{
    public $contentData;
    public $subjectText;

    public function __construct($transaction, $key)
    {
        $email = email_template($key);

        $data = [
            'user.name' => $transaction->customer->user->name,
            'store.name' => $transaction->store->name,
            'transaction.code' => $transaction->invoice_code,
            'transaction.total' => number_format($transaction->total, 0, ',', '.'),
            'transaction.date' => $transaction->created_at->format('d M Y H:i'),
            'transaction.status' => strtoupper($transaction->status),
            'url.invoice' => url('/transactions/' . $transaction->id),
        ];

        $this->contentData = parse_template($email->body, $data);
        $this->subjectText = parse_template($email->subject, $data);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dynamic',
            with: [
                'content' => $this->contentData,
                'store_name' => config('app.name')
            ]
        );
    }
}

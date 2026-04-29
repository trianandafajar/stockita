<?php

namespace App\Notifications;

use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class StockLowNotification extends Notification
{
    use Queueable;

    protected $stock;
    /**
     * Create a new notification instance.
     */
    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database', WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $url = $notifiable->hasRole('admin')
            ? '/admin/warehouse/' . $this->stock->warehouse_id
            : '/warehouse/' . $this->stock->warehouse_id;

        return (new WebPushMessage)
            ->title('Stok Menipis')
            ->body($this->stock->product->name . ' sisa ' . $this->stock->qty)
            ->icon('/favicon.ico')
            ->data(['url' => $url]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Stok Menipis',
            'message' => $this->stock->product->name . ' sisa ' . $this->stock->qty,
            'url' => '/warehouse/' . $this->stock->warehouse_id,
        ];
    }
}

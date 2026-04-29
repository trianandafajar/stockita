<?php

namespace App\Notifications;

use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class StockOutNotification extends Notification
{
    use Queueable;

    protected $stock;

    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

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
            ->title('Stok Habis')
            ->body($this->stock->product->name . ' - ' . $this->stock->warehouse->name)
            ->icon('/favicon.ico')
            ->data(['url' => $url]);
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Stok Habis',
            'message' => $this->stock->product->name . ' - ' . $this->stock->warehouse->name,
            'url' => '/warehouse/' . $this->stock->warehouse_id,
        ];
    }
}

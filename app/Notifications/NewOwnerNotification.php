<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewOwnerNotification extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        return (new WebPushMessage)
            ->title('New Owner')
            ->body($this->user->name . ' has just registered')
            ->icon('/favicon.ico')
            ->data(['url' => '/admin/store/' . ($this->user->store->id ?? '')]);
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'success',
            'title' => 'New Owner',
            'message' => $this->user->name . ' has just registered',
            'url' => '/store/' . ($this->user->store->id ?? ''),
        ];
    }
}

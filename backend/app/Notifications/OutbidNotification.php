<?php

namespace App\Notifications;

use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OutbidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Bid $bid,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $item = $this->bid->item;
        $amount = number_format($this->bid->amount, 2);

        return (new MailMessage)
            ->subject("You've been outbid on {$item->title}")
            ->view('emails.outbid', [
                'itemTitle' => $item->title,
                'amount' => $amount,
                'itemUrl' => url("/items/{$item->id}"),
            ]);
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $item = $this->bid->item;

        return new BroadcastMessage([
            'item_id' => $item->id,
            'item_title' => $item->title,
            'amount' => $this->bid->amount,
            'message' => "You've been outbid on {$item->title}!",
        ]);
    }
}

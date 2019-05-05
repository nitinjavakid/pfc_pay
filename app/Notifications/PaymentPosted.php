<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class PaymentPosted extends Notification
{
    use Queueable;

    private $user;
    private $event;
    private $price;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $event, $price)
    {
        $this->user = $user;
        $this->event = $event;
        $this->price = $price;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
	   ->title('Payment posted')
	   ->body('Your payment of ' . $this->price . ' was posted for event ' . $this->event->name . " " . $this->event->local_time)
           ->data("/passbook");
    }
}

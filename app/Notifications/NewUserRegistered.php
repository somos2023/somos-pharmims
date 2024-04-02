<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Facades\Agent;

class NewUserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    // public function __construct(private string $browser)
    // {
    //   //
    // }

    // /**
    //  * Get the notification's delivery channels.
    //  *
    //  * @param  mixed  $notifiable
    //  * @return array
    //  */
    // public function via($notifiable)
    // {
    //     return ['vonage'];
    // }

    // /**
    //  * Get the Vonage / SMS representation of the notification.
    //  *
    //  * @param  mixed  $notifiable
    //  * @return \Illuminate\Notifications\Messages\VonageMessage
    //  */
    // public function toVonage($notifiable)
    // {
    //     return (new VonageMessage())
    //         ->from('E-DENTS')
    //         ->content('You have now registered for E-DENTS APP!');
    // }
}
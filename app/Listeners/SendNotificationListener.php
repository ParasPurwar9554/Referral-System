<?php

namespace App\Listeners;

use App\Events\SendNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SendNotification  $event
     * @return void
     */
    public function handle(SendNotification $event)
    {
        $data['url'] = $event->email['url'];
        $data['name'] = $event->email['name'];  
        $data['email'] = $event->email['email'];
        $data['password'] = '';
        $data['title'] = $event->email['title'];

     Mail::send('emails.registerMail', ['data' => $data], function ($message) use ($data) {
       $message->to('paraspurwar5@gmail.com')->subject($data['title']);
     });


    }
}

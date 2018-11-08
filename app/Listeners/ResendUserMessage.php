<?php

namespace App\Listeners;

use App\Events\UserMessagesNotSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

class ResendUserMessage implements ShouldQueue
{
    use InteractsWithQueue;
    public $connection = 'sqs';
    public $queue = 'listeners';
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
     * @param  UserMessagesNotSent  $event
     * @return void
     */
    public function handle(UserMessagesNotSent $event)
    {
        echo "Resend123!" . PHP_EOL;
        Artisan::call('sendUserMessages:specific-user', [
            'user_id' => rand(1,4), 'message' => 'default'
        ]);
    }
}

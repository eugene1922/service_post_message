<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\SendMessageTrait;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class SendSpecificUserMessage extends Command
{
    use SendMessageTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendUserMessages:specific-user {user_id} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->userId = $this->argument('user_id');
        $this->message = $this->argument('message');

        $this->handleSendMessage($this->userId, $this->message);
        
        $callback = function ($msg) {
            $data = json_decode($msg->body);

            echo ' [*] Wait - resend ', $data->user_id, '-', $data->message, "\n";

            $this->handleSendMessage($this->userId, $this->message);
        };

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->basic_consume('send_user_message', '', false, true, false, false, $callback);
    }

}

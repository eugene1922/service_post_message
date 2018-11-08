<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\UserMessagesNotSent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\DB;

class SendUserMessages extends Command
{
	use SendMessageTrait;	
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendUserMessages:to-all-users { numberProcesses=1 : The number of processes to run }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the command that read messages from table user_messages and send it';

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
		$processes_number = $this->argument('numberProcesses');
		$i = 0;

		while($i < $processes_number) {
			$i++;

	        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
	        $channel = $connection->channel();
	        $channel->queue_declare('process_send_user_message', false, false, false, false);
	        $messageBody = "processes_number: $i";
	        $msg = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
	        $channel->basic_publish($msg, '', 'process_send_user_message');

            echo "Set proccess number - $i" .  PHP_EOL;
		}

        $callback = function ($msg) {
            $data = json_decode($msg->body);

            echo ' [*] Wait - resend ', $data->user_id, '-', $data->message, "\n";

            $this->handleSendMessage($data->user_id, $data->message);
        };
        
        $channel->basic_consume('send_user_message', '', false, true, false, false, $callback);
        $channel->basic_consume('process_send_user_message', '', false, true, false, false, [$this, 'handleProcess']);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function handleProcess($msg)
    {
    	echo $msg->body . PHP_EOL;

        $userMessages = DB::select("SELECT `user_id`, `message` FROM `user_messages` LIMIT 1");

        foreach ($userMessages as $userMessage) {
        	$this->handleSendMessage($userMessage->user_id, $userMessage->message);
        }
    }

}

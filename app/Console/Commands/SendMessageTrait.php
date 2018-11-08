<?php

namespace App\Console\Commands;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

trait SendMessageTrait
{
    public function sendMessage($userId, $message) : bool
    {
        if (empty($userId) || empty($message))
        {
            throw new \Exception('Один из аргументов содержит пустое значение');
        }

       # sleep(1);

        return boolval(rand(0, 1));
    }

    public function afterSendMessage($result, $userId, $message) : bool
    {
    	if (!$result) {
    		$this->setQueueTask($userId, $message);
    	}
    }

    public function setQueueTask($userId, $message)
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('send_user_message', false, false, false, false);
        $messageBody = json_encode(['user_id' => $userId, 'message' => $message]);
        $msg = new AMQPMessage($messageBody, ['content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel->basic_publish($msg, '', 'send_user_message');
        $channel->close();
        $connection->close();
    }
    public function handleSendMessage($userId, $message)
    {
        $result = $this->sendMessage($userId, $message);
        $this->handleResultSentMessage($result, $userId, $message);
    }

    public function handleResultSentMessage($result, $user_id, $message)
    {
        if ($result) {
            echo "Send message for user - $user_id, message - $message" . PHP_EOL;
        } else {
            $this->setQueueTask($user_id, $message);
        }
    }
}
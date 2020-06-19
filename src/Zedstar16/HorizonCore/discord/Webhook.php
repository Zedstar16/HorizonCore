<?php


declare(strict_types = 1);

namespace Zedstar16\HorizonCore\discord;

use Zedstar16\HorizonCore\discord\task\DiscordWebhookSendTask;
use pocketmine\Server;

class Webhook {
	/** @var string */
	protected $url;

	public function __construct(string $url){
		$this->url = $url;
	}

	public function getURL(): string{
		return $this->url;
	}

	public function send(Message $message): void{
		Server::getInstance()->getAsyncPool()->submitTask(new DiscordWebhookSendTask($this, $message));
	}
}
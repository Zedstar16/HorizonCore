<?php


namespace Zedstar16\HorizonCore\components\ChatHandler;


use Zedstar16\HorizonCore\HorizonPlayer;

class BaseChatHandler
{
    /** @var HorizonPlayer */
    public $p;
    /** @var String */
    public $msg;

    public $can_send = true;

    public $fail_message = "";

    public $resultant_message = "";

    public $sensitive = false;

    public function __construct(HorizonPlayer $player, string $message)
    {
        $this->p = $player;
        $this->msg = $message;
    }

    public function getFormattedMessage(): ?string
    {
        $filter = new ChatFilter($this->msg);
        $formatter = new Formatter($this->p);
        $cleaned = $filter->getCleanedMessage();
        if ($filter->cansend) {
            return $formatter->getFormattedMessage() . $filter->getCleanedMessage();
        }
        $this->p->sendMessage("§cYou cannot send a message with these characters");
        return null;
    }

    public function getResultant(): array
    {
        return [
            "can_send" => $this->can_send,
            "sensitive" => $this->sensitive,
            "fail" => $this->fail_message,
            "message" => $this->resultant_message
        ];
    }
}

<?php


namespace Zedstar16\HorizonCore\components\ChatHandler;


use Zedstar16\HorizonCore\HorizonPlayer;

class BaseChatHandler
{
    /** @var HorizonPlayer */
    public $p;
    /** @var String */
    public $msg;

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
        } else {
            $this->p->sendMessage("§cYou cannot send a message with these characters");
            return null;
        }
    }
}

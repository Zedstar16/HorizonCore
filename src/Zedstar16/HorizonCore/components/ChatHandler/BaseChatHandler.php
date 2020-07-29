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

    public function getFormattedMessage()
    {
        $filter = new ChatFilter($this->msg);
        $formatter = new Formatter()
        $cleaned = $filter->getCleanedMessage();

    }

}

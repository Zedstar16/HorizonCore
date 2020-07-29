<?php


namespace Zedstar16\HorizonCore\components\ChatHandler;


use Zedstar16\HorizonCore\HorizonPlayer;

class Formatter
{
    /** @var HorizonPlayer */
    public $p;

    public function __construct(HorizonPlayer $player)
    {
        $this->p = $player;
    }

    public function getFormattedMessage()
    {
        return "";
    }


}
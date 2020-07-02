<?php


namespace Zedstar16\HorizonCore\events;


use Zedstar16\HorizonCore\HorizonPlayer;

class AddCoinsEvent extends HorizonPlayerEvent
{

    public function __construct(HorizonPlayer $player)
    {
        parent::__construct($player);
    }

    public function getNewBalance()
    {
        return $this->player->getEconomy()->getCoins();
    }

}
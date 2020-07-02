<?php


namespace Zedstar16\HorizonCore\events;

use Zedstar16\HorizonCore\HorizonPlayer;

class AddKillStreakEvent extends HorizonPlayerEvent
{
    public function __construct(HorizonPlayer $player)
    {
        parent::__construct($player);
    }

    public function getKillStreak(){
        return $this->player->getSession()->getKillStreak();
    }


}
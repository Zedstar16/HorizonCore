<?php


namespace Zedstar16\HorizonCore\events;



class AddKillStreakEvent extends HorizonPlayerEvent
{

    public function __construct()
    {
        $this->call();
    }

    public function getKillStreak(){
        return $this->player->getSession()->getKillStreak();
    }


}
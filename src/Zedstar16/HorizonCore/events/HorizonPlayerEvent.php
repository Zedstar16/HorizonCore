<?php


namespace Zedstar16\HorizonCore\events;


use pocketmine\event\Event;
use pocketmine\Player;
use Zedstar16\HorizonCore\HorizonPlayer;

abstract class HorizonPlayerEvent extends Event{
    /** @var HorizonPlayer */
    protected $player;

    public function getPlayer() : HorizonPlayer{
        return $this->player;
    }
}
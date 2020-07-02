<?php


namespace Zedstar16\HorizonCore\events;


use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class PlayerClickEvent extends HorizonPlayerEvent
{

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->call();
    }

}
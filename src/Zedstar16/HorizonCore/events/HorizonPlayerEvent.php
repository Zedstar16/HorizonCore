<?php


namespace Zedstar16\HorizonCore\events;


use pocketmine\event\Event;
use Zedstar16\HorizonCore\HorizonPlayer;

abstract class HorizonPlayerEvent extends Event
{
    /** @var HorizonPlayer */
    public $player;

    public function __construct(HorizonPlayer $player)
    {
        $this->player = $player;
        $this->call();
    }

    public function getPlayer(): HorizonPlayer
    {
        return $this->player;
    }
}
<?php


namespace Zedstar16\HorizonCore\events;


use pocketmine\event\Event;
use Zedstar16\HorizonCore\Horizon;

class HorizonEvent extends Event
{
    /** @var Horizon */
    protected $horizon;

    public function __construct(Horizon $horizon)
    {
        $this->horizon = $horizon;
        $this->call();
    }

    public function getHorizon() : Horizon{
       return $this->horizon;
    }

}
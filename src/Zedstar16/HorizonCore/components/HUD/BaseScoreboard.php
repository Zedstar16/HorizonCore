<?php


namespace Zedstar16\HorizonCore\components\HUD;


use Zedstar16\HorizonCore\HorizonPlayer;

abstract class BaseScoreboard
{
    /** @var HorizonPlayer */
    public $p;

    public function __construct(HorizonPlayer $player)
    {
        $this->p = $player;
    }

    public function getLines(): array
    {
        return [];
    }

}
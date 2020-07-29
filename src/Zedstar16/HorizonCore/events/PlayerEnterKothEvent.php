<?php


namespace Zedstar16\HorizonCore\events;


use Zedstar16\HorizonCore\components\KOTH\Koth;
use Zedstar16\HorizonCore\HorizonPlayer;

class PlayerEnterKothEvent extends HorizonPlayerEvent
{
    /** @var Koth $koth */
    private $koth;

    public function __construct(HorizonPlayer $player, Koth $koth)
    {
        $this->koth = $koth;
        parent::__construct($player);
    }

    public function getKoth(): Koth
    {
        return $this->koth;
    }

}
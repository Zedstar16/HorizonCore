<?php


namespace Zedstar16\HorizonCore\components\HUD;


use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\ZDuels\Main;

class Duel extends BaseScoreboard
{
    public function __construct(HorizonPlayer $player)
    {
        parent::__construct($player);
    }

    public function getLines(): array
    {
        $duel = Main::getDuelManager()->getDuel($this->p);
        if ($duel === null) {
            return ["Status" => "Error"];
        }
        $op = $duel->getOpponent($this->p);
        return [
            "Opponent" => $op->getName(),
            "Their Ping" => $op->getPing(),
            "Your Ping" => $this->p->getPing(),
            "Time Remaining" => gmdate("i:s", $duel->timeRemaining)
        ];
    }
}
<?php


namespace Zedstar16\HorizonCore\components\HUD;


use Zedstar16\HorizonCore\HorizonPlayer;

class KitPvP extends BaseScoreboard
{
    public function __construct(HorizonPlayer $player)
    {
        parent::__construct($player);
    }

    public function getLines(): array
    {
        return [
            "Online" => count($this->p->getServer()->getOnlinePlayers()),
            "User" => $this->p->getName(),
            "Rank" => "Owner",
            "Level" => $this->p->getExperience()->getLevel(),
            "Area" => "KitPvP",
            "Area Players" => count($this->p->getLevel()->getPlayers()),
            "Coins" => $this->p->getEconomy()->getCoins()
        ];
    }

}
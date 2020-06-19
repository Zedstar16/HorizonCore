<?php

namespace Zedstar16\HorizonCore\hud;

use pocketmine\Player;
use Zedstar16\HorizonCore\libs\JackMD\ScoreFactory\ScoreFactory;

class CreateScoreboard
{
    public static function add(Player $player){
        ScoreFactory::setScore($player, "§6§lHorizon§cPE");
    }

}
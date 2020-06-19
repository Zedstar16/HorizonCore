<?php

namespace Zedstar16\HorizonCore\hud;

use Zedstar16\HorizonCore\libs\JackMD\ScoreFactory\ScoreFactory;
use pocketmine\Player;

class UpdateScoreboard
{

    public static function update(Player $player){
        ScoreFactory::setScoreLine($player, 1, "§ePing: {$player->getPing()} ms");
    }

}
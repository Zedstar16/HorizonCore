<?php


namespace Zedstar16\HorizonCore\tasks;


use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zedstar16\HorizonCore\hud\UpdateScoreboard;

class UpdateScoreboardTask extends Task
{

    private $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function onRun(Int $currentTick)
    {
        if (Server::getInstance()->getPlayer($this->player->getName()) !== null) {
            UpdateScoreboard::update($this->player);
        } else {
            $this->getHandler()->cancel();
        }
    }

}
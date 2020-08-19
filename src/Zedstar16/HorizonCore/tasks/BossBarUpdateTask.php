<?php


namespace Zedstar16\HorizonCore\tasks;


use pocketmine\item\ItemIds;
use pocketmine\item\Potion;
use pocketmine\item\SplashPotion;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\BossBarTitles;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\libs\xenialdan\apibossbar\BossBar;
use Zedstar16\HorizonCore\libs\xenialdan\apibossbar\DiverseBossBar;

class BossBarUpdateTask extends Task
{
    public $player, $i;


    public function __construct(HorizonPlayer $player)
    {
        $this->player = $player;
        $this->i = 1;
    }

    public function onRun(int $currentTick)
    {
        if ($this->i >= count(BossBarTitles::TITLES)) {
            $this->i = 0;
        }
        if ($this->player->getSession() === null) {
            $this->getHandler()->cancel();
        } else {
            if ($this->player->getSession()->getBossBar() !== null) {
                $this->player->getSession()->getBossBar()->setSubTitle(BossBarTitles::TITLES[$this->i]);
                $this->i++;
            }
        }
    }

}
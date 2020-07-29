<?php


namespace Zedstar16\HorizonCore\tasks;


use pocketmine\scheduler\Task;
use Zedstar16\HorizonCore\components\KOTH\Koth;
use Zedstar16\HorizonCore\components\KOTH\KothGameManager;

class KothGameTick extends Task
{

    public $koth;

    public function __construct(Koth $koth)
    {
        $this->koth = $koth;
    }

    public function onRun(int $currentTick)
    {
        if ($this->koth !== null) {
            if ($this->koth->ended) {
                unset($this->koth);
                $this->getHandler()->cancel();
                KothGameManager::$current_game = null;
            } else {
                $this->koth->tick();
            }
        } else {
            KothGameManager::$current_game = null;
            $this->getHandler()->cancel();
        }
    }
}
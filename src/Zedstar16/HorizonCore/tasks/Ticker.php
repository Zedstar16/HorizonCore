<?php


namespace Zedstar16\HorizonCore\tasks;


use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\CPS;
use Zedstar16\HorizonCore\HorizonPlayer;

class Ticker extends Task
{

    public function onRun(int $currentTick)
    {
       foreach (Server::getInstance()->getOnlinePlayers() as $p){
           if(isset(CPS::$last_cps[$p->getName()])) {
               if ((microtime(true) - CPS::$last_cps[$p->getName()]["time"]) > 1 && $p instanceof HorizonPlayer) {
                   $p->updateNameTag();
               }
           }
       }
    }
}
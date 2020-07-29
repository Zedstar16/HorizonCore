<?php


namespace Zedstar16\HorizonCore\components\KOTH;

use pocketmine\Server;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\tasks\KothGameTick;

class KothGameManager
{
    /** @var Koth|null */
    public static $current_game;

    public static function startGame()
    {
        self::$current_game = new Koth(Server::getInstance());
        Horizon::getInstance()->getScheduler()->scheduleRepeatingTask(new KothGameTick(self::$current_game), 20);
    }


    /**
     * @return Koth|null
     */
    public static function getCurrentGame(): ?Koth
    {
        return self::$current_game;
    }

}
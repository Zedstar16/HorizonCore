<?php


namespace Zedstar16\HorizonCore\managers;


use pocketmine\Server;
use Zedstar16\HorizonCore\components\FFA\FFA;
use Zedstar16\HorizonCore\components\WorldMap;
use Zedstar16\HorizonCore\Horizon;

class FFAManager
{
    /** @var FFA[] */
    public static $arenas = [];

    public static function loadArenas()
    {
        $i = 0;
        foreach(self::getArenaNames() as $name){
            self::$arenas[] = new FFA($name);
            $i++;
        }
        Horizon::getInstance()->getLogger()->notice("$i FFA Maps Loaded");
    }

    public function getArena(string $name): ?FFA
    {
        foreach (self::$arenas as $arena) {
            if ($arena->getName() == $name) {
                return $arena;
            }
        }
        return null;
    }

    private static function getArenaNames()
    {
        $list = glob(Horizon::getInstance()->getDataFolder() . "ffa/*");
        $names = [];
        foreach ($list as $file) {
            $names[] = substr($file, 0, strlen($file) - 4);
        }
        return $names;
    }

    public static function getFFAArenaPlayers(): array
    {
        $players = [];
        foreach (self::$arenas as $arena)
        {
            foreach(Server::getInstance()->getLevelByName(WorldMap::FFA()[$arena->getName()])->getPlayers() as $p){
                $players[] = $p;
            }
        }
        return $players;
    }
}
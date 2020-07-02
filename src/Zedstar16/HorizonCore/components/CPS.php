<?php


namespace Zedstar16\HorizonCore\components;

use pocketmine\Player;

class CPS
{

    public static $data = [];

    public static $last_cps = [];

    public static function addClick(Player $p)
    {
        $name = $p->getName();
        if (!isset(self::$data[$name])) {
            self::$data[$name] = [];
        }
        $time = microtime(true);
        array_unshift(self::$data[$name], $time);
        if (count(self::$data[$name]) >= 101) {
            array_pop(self::$data[$name]);
        }
    }

    public static function calculateCPS(Player $p): int
    {
        $name = $p->getName();
        $time = microtime(true);
        $cps = 0;
        if (isset(self::$data[$name]) && !empty(self::$data[$name])) {
            $cps = count(array_filter(self::$data[$name], static function (float $t) use ($time) : bool {
                return ($time - $t) <= 1;
            }));
        }
        self::$last_cps[$name] = [
            "time" => microtime(true),
            "cps" => $cps
        ];
        return $cps / 2;
    }

}
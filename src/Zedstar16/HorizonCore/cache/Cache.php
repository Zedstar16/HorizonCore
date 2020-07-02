<?php


namespace Zedstar16\HorizonCore\cache;


use Zedstar16\HorizonCore\managers\DatabaseManager;
use Zedstar16\HorizonCore\managers\FileManager;

class Cache
{

    public static $data = [];
    public static $altered = [];

    public static function write(){
        foreach(self::$altered as $username){
            DatabaseManager::savePlayerData($username, self::$data[$username]);
            unset(self::$altered[$username]);
        }
    }

}
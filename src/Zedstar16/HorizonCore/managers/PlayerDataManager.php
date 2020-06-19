<?php


namespace Zedstar16\HorizonCore\managers;

use pocketmine\Player;
use pocketmine\Server;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;

class PlayerDataManager
{

    public static function getData(string $playername)
    {
        if(!self::hasData($playername)){
            self::init(Horizon::getPlayer($playername));
        }
        return FileManager::getJsonData("players/$playername");
    }

    public static function saveData(string $playername, array $data)
    {
        FileManager::saveJsonData("players/".$playername, $data);
    }

    public static function hasData(string $playername){
        return file_exists(Horizon::getInstance()->getDataFolder()."players/$playername.json");
    }

    public static function init(HorizonPlayer $player)
    {
        $name = $player->getLowerCaseName();
        $data = [
            "ips" => [],
            "cids" => [],
            "deviceids" => [],
            "hits" => 0,
            "damage_taken" => 0,
            "damage_dealt" => 0,
            "clicks" => 0,
            "distance_travelled" => 0,
            "kills" => 0,
            "deaths" => 0,
            "joins" => 0,
            "chat_messages" => 0,
            "dropped_items" => 0,
            "items_picked_up" => 0,
            "items_consumed" => 0,
            "blocks_placed" => 0,
            "blocked_broken" => 0,
            "last_seen_data" => [
                "ip" => "",
                "cid" => "",
                "deviceid" => "",
                "os" => "",
                "ui" => "",
                "controls" => "",
                "timestamp" => ""
            ]
        ];
        FileManager::saveJsonData("players/".$name, $data);
    }


}
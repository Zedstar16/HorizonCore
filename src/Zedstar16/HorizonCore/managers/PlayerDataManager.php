<?php


namespace Zedstar16\HorizonCore\managers;

use pocketmine\Player;
use pocketmine\Server;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\utils\Utils;

class PlayerDataManager
{

    public static function getData($player)
    {
        $player = Utils::stringify($player);
        if (!self::isRegistered($player)) {
            self::init(Horizon::getPlayer($player));
        }
        return FileManager::getJsonData("players/$player");
    }

    public static function saveData($player, array $data)
    {
        $player = Utils::stringify($player);
        FileManager::saveJsonData("players/" . $player, $data);
    }

    public static function isRegistered($player)
    {
        $player = Utils::stringify($player);
        return file_exists(Horizon::getInstance()->getDataFolder() . "players/$player.json");
    }

    public static function incrementValue($player, $key, $value){
        $data = self::getData($player);
        $data[$key] += $value;
        self::saveData($player, $data);
    }


    public static function init($player)
    {
        $playername = Utils::stringify($player);
        $player = Server::getInstance()->getPlayer($playername);
        if (!self::isRegistered($player)) {
            $data = [
                "username-cased" => $player->getName(),
                "ips" => [],
                "cids" => [],
                "deviceids" => [],
                "hits" => 0,
                "experience" => 0,
                "coins" => 0,
                "damage_taken" => 0,
                "damage_dealt" => 0,
                "clicks" => 0,
                "distance_travelled" => 0,
                "kills" => 0,
                "deaths" => 0,
                "joins" => 0,
                "chat_messages" => 0,
                "items_dropped" => 0,
                "items_picked_up" => 0,
                "items_consumed" => 0,
                "blocks_placed" => 0,
                "blocks_broken" => 0,
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
            FileManager::saveJsonData("players/" . $playername, $data);
        }
    }


}
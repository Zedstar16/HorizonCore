<?php


namespace Zedstar16\HorizonCore\managers;

use pocketmine\Server;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\utils\Utils;

class PlayerDataManager
{

    public static function getData($player)
    {
        $player = Utils::stringify($player);
        if (!self::isRegistered($player)) {
            Server::getInstance()->getLogger()->notice("New player registered: Zedstar16");
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

    public static function incrementValue($player, $key, $value)
    {
        $data = self::getData($player);
        $data[$key] += $value;
        self::saveData($player, $data);
    }

    public static function login($player)
    {
        $data = self::getData($player);
        $playername = Utils::stringify($player);
        $player = Horizon::getPlayer($playername);
        if (!in_array($player->getAddress(), $data["ips"], true)) {
            $data["ips"][] = $player->getAddress();
        }
        if (!in_array($player->getClientId(), $data["cids"], true)) {
            $data["cids"][] = $player->getClientId();
        }
        if (!in_array($player->getAddress(), $data["deviceids"], true)) {
            $data["deviceids"][] = $player->getAddress();
        }
        $data["last_seen_data"] = [
            "ip" => $player->getAddress(),
            "cid" => $player->getClientId(),
            "deviceid" => $player->getSession()->getPlayerData()["DeviceID"],
            "os" => $player->getSession()->getPlayerData()["OS"],
            "ui" => $player->getSession()->getPlayerData()["UI"],
            "controls" => $player->getSession()->getPlayerData()["Controls"],
            "timestamp" => time()
        ];
        self::saveData($player, $data);
    }


    public static function init($player)
    {
        $playername = Utils::stringify($player);
        $player = Horizon::getPlayer($playername);
        //var_dump(SessionManager::$sessions);
        if (!self::isRegistered($player)) {
            $data = [
                "username-cased" => $player->getName(),
                "xuid" => $player->getXuid(),
                "ips" => [$player->getAddress()],
                "cids" => [$player->getClientId()],
                "deviceids" => [$player->getSession()->getPlayerData()["DeviceID"]],
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
                    "ip" => $player->getAddress(),
                    "cid" => $player->getClientId(),
                    "deviceid" => $player->getSession()->getPlayerData()["DeviceID"],
                    "os" => $player->getSession()->getPlayerData()["OS"],
                    "ui" => $player->getSession()->getPlayerData()["UI"],
                    "controls" => $player->getSession()->getPlayerData()["Controls"],
                    "timestamp" => time()
                ]
            ];
            FileManager::saveJsonData("players/" . $playername, $data);
        }
    }


}
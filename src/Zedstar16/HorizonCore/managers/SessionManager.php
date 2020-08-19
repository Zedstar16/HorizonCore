<?php


namespace Zedstar16\HorizonCore\managers;


use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\session\Session;

class SessionManager
{
    /** @var Session[] */
    public static $sessions = [];

    public static function add(HorizonPlayer $player)
    {
        echo "a";
        $session = new Session($player);
        echo "b";
        $session->clientData = Horizon::$players[$player->getName()]["clientData"];
        self::$sessions[$player->getName()] = $session;
        //var_dump(self::$sessions);
        echo "c";
        echo "d";
    }

    public static function getSession(HorizonPlayer $player)
    {
        return self::$sessions[$player->getName()] ?? null;
    }

    public static function remove(HorizonPlayer $player){
        $session = self::getSession($player);
        $session_data = $session->getSessionStats();
     //   var_dump($session_data);
        $player_data = PlayerDataManager::getData($player);
        foreach($session_data as $key => $value){
            $player_data[$key] += $value;
        }
        PlayerDataManager::saveData($player, $player_data);
        unset(self::$sessions[$player->getName()], $session);
    }

}
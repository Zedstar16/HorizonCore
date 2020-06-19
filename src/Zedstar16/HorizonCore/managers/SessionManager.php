<?php


namespace Zedstar16\HorizonCore\managers;


use pocketmine\Server;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\session\Session;

class SessionManager
{
    /** @var Session[] */
    private static $seassions = [];

    public static function add(HorizonPlayer $player)
    {
        Server::getInstance()->getLogger()->info("Added {$player->getName()}");
        $session = new Session($player);
        self::$seassions[] = $session;
        $session->clientData = Horizon::$players[$player->getName()]["clientData"];
    }

    public static function getSession(HorizonPlayer $player)
    {
        foreach (self::$seassions as $session) {
            if ($session->getPlayer() === $player) {
                return $session;
            }
        }
        return null;
    }

    public static function remove(HorizonPlayer $player){
        foreach (self::$seassions as $key => $session) {
            if ($session->getPlayer() === $player) {
                unset(self::$seassions[$key]);
                unset($session);
            }
        }
    }

}
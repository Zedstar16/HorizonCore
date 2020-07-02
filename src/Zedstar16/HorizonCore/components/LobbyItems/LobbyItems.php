<?php

namespace Zedstar16\HorizonCore\components\LobbyItems;

use pocketmine\Player;
use pocketmine\Server;

class LobbyItems
{
    /** @var Player */
    public $p;

    public function __construct(Player $player, string $tag)
    {
        $this->p = $player;
        switch ($tag) {
            case "ffa":
                new FFAForm($player);
                break;
            case "duels":
                new DuelForm($player);
                break;
            case "profile":
                new ProfileForm($player);
                break;
            case "stats":
                new StatsForm($player);
                break;
            case "toys":
                new CosmeticsToysForm($player);
                break;
            case "settings";
                new SettingsForm($player);
                break;
            case "info":
                Server::getInstance()->dispatchCommand($player, "info");
                break;
        }
    }

    public function Stats()
    {

    }

}
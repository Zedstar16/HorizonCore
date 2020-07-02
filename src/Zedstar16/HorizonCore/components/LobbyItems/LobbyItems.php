<?php

namespace Zedstar16\HorizonCore\components\LobbyItems;

use pocketmine\Player;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

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
        }
    }

    public function Stats()
    {

    }

}
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
                new Profile($player);
                break;
            case "stats":
                $this->Stats();
                break;
                case "settings";

                break;
        }
    }

    public function Stats()
    {

    }

}
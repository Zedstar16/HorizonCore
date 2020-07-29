<?php


namespace Zedstar16\HorizonCore\commands\player;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Zedstar16\HorizonCore\components\WorldMap;
use Zedstar16\HorizonCore\HorizonPlayer;

class HubCommand extends Command
{


    public function __construct()
    {
        parent::__construct("hub");
        $this->description = "Teleport to server spawn";
        $this->setPermission("horizon.player");
        $this->setAliases(["spawn"]);
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof HorizonPlayer) {
            $sender->teleportToSpawn();
            $sender->sendMessage("§aTeleported to spawn");
            $sender->setInSpawn(true, $sender->getLevel()->getName() === WorldMap::KIT);
        }
    }

}
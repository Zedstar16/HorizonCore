<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zedstar16\HorizonCore\managers\BossManager;

class BossCommand extends Command
{


    public function __construct()
    {
        parent::__construct("boss");
        $this->description = "spawn a boss";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.admin");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $pos = $sender->getPosition();
            if (isset($args[0])) {
                switch ($args[0]) {
                    case "snowman":
                        $sender->sendMessage(BossManager::spawnBoss($pos, "SnowmanBoss") ? "§aSuccessfully spawened boss at your position" : "§cFailed to spawn boss");
                        break;
                }
            }
        }
    }

}
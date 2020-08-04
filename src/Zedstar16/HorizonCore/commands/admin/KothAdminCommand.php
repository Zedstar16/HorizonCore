<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Zedstar16\HorizonCore\components\KOTH\KothGameManager;
use Zedstar16\HorizonCore\components\KOTH\KothUtils;

class KothAdminCommand extends Command
{


    public function __construct()
    {
        parent::__construct("koth");
        $this->description = "koth admin command";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.cmd");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[0])) {
            switch ($args[0]) {
                case "start":
                    echo "run";
                    KothGameManager::startGame();
                    break;
                case "a":
                    KothUtils::setFloor(true);
                    break;
                case "d":
                    KothUtils::setFloor(false);
                    break;
            }
        }
    }

}
<?php


namespace Zedstar16\HorizonCore\commands\player;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class ClanCommand extends Command
{


    public function __construct()
    {
        parent::__construct("clan");
        $this->description = "Clan Management Commands";
        $this->usageMessage = "";
        $this->setPermission("horizon.clan");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[0])) {
            switch ($args[0]) {
                case "sub":
                    echo "hi";
                    break;
            }
        }
    }

}
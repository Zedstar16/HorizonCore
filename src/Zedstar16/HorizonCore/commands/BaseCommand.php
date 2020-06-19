<?php


namespace Zedstar16\HorizonCore\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class BaseCommand extends Command
{


    public function __construct()
    {
        parent::__construct("cmdname");
        $this->description = "cmd desc";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.cmd");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(isset($args[0])){
            switch($args[0]){
                case "sub":
                    echo "hi";
                break;
            }
        }
    }

}
<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Zedstar16\HorizonCore\managers\FileManager;

class ConfigurationsCommand extends Command
{


    public function __construct()
    {
        parent::__construct("conf");
        $this->description = "Configurations for stuff related to the Horizon internals";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.admin");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[0])) {
            switch ($args[0]) {
                case "crate":
                    echo "hi";
                    break;
                case "chestkit":
                    $cfg = FileManager::getYamlData("config.yml");
                    $cfg["chestkit"][$args[0]];
                    break;
            }
        }
    }

}
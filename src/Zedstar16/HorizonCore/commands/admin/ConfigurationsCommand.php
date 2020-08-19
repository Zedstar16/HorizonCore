<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\FloatingTextManager;
use Zedstar16\HorizonCore\managers\KitManager;

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
        if (isset($args[0]) && $sender instanceof Player) {
            $contents = [];
            for ($i = 9; $i <= 35; $i++) {
                $contents[] = KitManager::indexItem($sender->getInventory()->getItem($i));
            }
            switch ($args[0]) {
                case "crate":
                    $cfg = FileManager::getJsonData("conf");
                    $cfg["crate"][$args[1]] = $contents;
                    FileManager::saveYamlData("conf", $cfg);
                    break;
                case "chestkit":
                    $cfg = FileManager::getJsonData("conf");
                    $cfg["chestkit"][$args[1]]["contents"] = $contents;
                    $cfg["chestkit"][$args[1]]["pos"] = [
                        "x" => $sender->getFloorX(),
                        "y" => $sender->getFloorY(),
                        "z" => $sender->getFloorZ(),
                    ];
                    FloatingTextManager::add($sender->getLevel(), new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()), "chest-kit-$args[1]", "§7Tap to view");
                    FileManager::saveJsonData("conf", $cfg);
                    break;
            }
        }
    }

}
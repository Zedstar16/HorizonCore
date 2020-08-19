<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Zedstar16\HorizonCore\components\KitEditor;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\KitManager;

class SetKitCommand extends Command
{

    public function __construct()
    {
        parent::__construct("setkit");
        $this->description = "set kit";
        $this->usageMessage = "/setkit";
        $this->setPermission("horizon.setkit");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (count($args) >= 2 && $sender instanceof HorizonPlayer) {
            switch ($args[0]) {
                case "ffa":
                    $kit = FileManager::getYamlData("ffa/$args[1]");
                    $kit["armor"] = KitManager::indexContents($sender->getArmorInventory()->getContents());
                    $kit["inventory"] = KitManager::indexContents($sender->getInventory()->getContents());
                    FileManager::saveYamlData("ffa/$args[1]", $kit);
                    break;
                case "kit":
                    $kit = FileManager::getYamlData("kits/$args[1]");
                    $kit["armor"] = KitManager::indexContents($sender->getArmorInventory()->getContents());
                    $kit["inventory"] = KitManager::indexContents($sender->getInventory()->getContents());
                    FileManager::saveYamlData("kits/$args[1]", $kit);
                    break;
                case "test":
                    new KitEditor($sender);
                    break;
                case "rename":
                    if (isset($args[1])) {
                        array_shift($args);
                        $name = implode(" ", $args);
                        $item = $sender->getInventory()->getItemInHand();
                        $item->setCustomName($name);
                        $sender->getInventory()->setItemInHand($item);
                        $sender->sendMessage("§aRenamed the item in your hand to §r$name");
                    } else $sender->sendMessage("§cUsage: §6/duela rename (new name)");
                    break;
            }
        }
    }




}
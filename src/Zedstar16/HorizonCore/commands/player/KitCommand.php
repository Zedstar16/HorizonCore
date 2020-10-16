<?php


namespace Zedstar16\HorizonCore\commands\player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Zedstar16\HorizonCore\components\Constants;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\managers\KitManager;

class KitCommand extends Command
{

    public function __construct()
    {
        parent::__construct("kit");
        $this->description = "Select a kit";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof HorizonPlayer) {
            if (isset($args[0])) {
                if (!$sender->has_claimed_kit_this_life) {
                    $kit = KitManager::getKit($sender, $args[0], Constants::KIT_NORMAL);
                    $sender->getInventory()->setContents($kit["inventory"]);
                    $sender->getArmorInventory()->setContents($kit["armor"]);
                    $sender->sendMessage("Claimed $args[0] kit");
                } else $sender->sendMessage("You have already claimed a kit this life");
            }
        }
    }

}
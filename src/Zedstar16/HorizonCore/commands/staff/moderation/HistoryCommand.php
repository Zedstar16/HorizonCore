<?php

namespace Zedstar16\HorizonCore\commands\staff\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\HorizonCore\managers\FileManager;

class HistoryCommand extends Command
{

    public function __construct()
    {
        parent::__construct("history");
        $this->description = "View punishment history for a player";
        $this->setPermission("horizon.staff");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[0])) {
            $history = FileManager::getJsonData("history");
            if (isset($history[$args[0]])) {
                $list = "";
                foreach ($history[$args[0]] as $punishment) {
                    $list .= str_repeat("§4-", 32) . "\n";
                    foreach ($punishment as $key => $value) {
                        $list .= "§c> §6" . ucfirst($key) . ": §e" . $value . "\n";
                    }
                }
                if ($sender instanceof Player) {
                    $form = new SimpleForm(function (Player $player, $data = null) {
                        if ($data === null) {
                            return;
                        }
                    });
                    $form->setTitle("Punishment history for $args[0]");
                    $form->setContent($list);
                    $form->addButton("Close");
                    $sender->sendForm($form);
                } else $sender->sendMessage("Punishment history for $args[0]\n" . $list);
            } else $sender->sendMessage("$args[0] has never been punished");
        } else $sender->sendMessage("§cYou need to specify a target player");
    }
}

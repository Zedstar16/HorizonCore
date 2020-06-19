<?php


namespace Zedstar16\HorizonCore\commands;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\HorizonCore\managers\FileManager;

class InfoCommand extends Command
{

    public function __construct()
    {
        parent::__construct("info");
        $this->description = "Open info UI";
        $this->usageMessage = "/info";
        $this->setPermission("horizon.info");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $this->sendCatergoryList($sender);
        }
    }

    public function sendCatergoryList(Player $player): void
    {
        $catergories = [""];
        $form = new SimpleForm(function (Player $player, Int $data = null) {
            switch ($data) {
                case 0:
                    break;

            }
        });

    }

}
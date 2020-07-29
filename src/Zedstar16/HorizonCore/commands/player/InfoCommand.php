<?php


namespace Zedstar16\HorizonCore\commands\player;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zedstar16\HorizonCore\components\InfoUI\MainInfoForm;

class InfoCommand extends Command
{

    public function __construct()
    {
        parent::__construct("info");
        $this->description = "cmd desc";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.player");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            new MainInfoForm($sender);
        } else $sender->sendMessage("You can only run this command in game");

    }

}